# Upload e download de arquivos

Upload é uma das superfícies de maior impacto: um único erro pode virar execução remota de código.

---

## 1. Pipeline seguro

```text
1. exigir autenticação e autorização (quem pode enviar, e para qual escopo)
2. limitar tamanho ANTES de ler o corpo inteiro
3. limitar quantidade de arquivos por requisição e por período (rate limit)
4. validar extensão por ALLOWLIST
5. validar o tipo REAL pelos bytes iniciais (magic number), não pelo Content-Type
6. validar coerência: extensão × tipo real × uso esperado
7. gerar nome novo (UUID) e descartar o nome enviado (guardar o original só como metadado)
8. armazenar fora da raiz web, em storage que NÃO executa código
9. caminho com escopo de tenant: {company_id}/{uuid}.{ext}
10. processar/normalizar (reencode de imagem) quando aplicável
11. varredura antivírus quando o arquivo for redistribuído a terceiros
12. servir com Content-Type fixo, nosniff e Content-Disposition adequado
13. registrar em auditoria: quem enviou, o quê, quando, tamanho, hash
```

---

## 2. Validação de tipo

`Content-Type` do request é declarado pelo cliente — não é evidência.

```javascript
// ❌
if (file.mimetype === 'image/png') aceitar();

// ✅ conferir os bytes
import { fileTypeFromBuffer } from 'file-type';

const tipo = await fileTypeFromBuffer(buffer);
const PERMITIDOS = new Set(['image/png', 'image/jpeg', 'image/webp']);
if (!tipo || !PERMITIDOS.has(tipo.mime)) throw new Error('tipo não permitido');
```

Extensão por allowlist e derivada do tipo real detectado, não da string enviada:

```text
❌ blacklist de .php, .exe, .sh   → sempre incompleta
✅ allowlist de .png, .jpg, .webp, .pdf
```

Nomes que enganam: `foto.jpg.php`, `foto.php.jpg` (com servidor mal configurado), `foto.PhP`, `foto.php%00.jpg`, `foto.php;.jpg`, nome com `../`, nome com RTL override, nome de 500 caracteres, nome com `\` no Windows.

---

## 3. Armazenamento

- **nunca** dentro de diretório servido com execução (`public/`, `www/`, `htdocs/`);
- preferir storage de objeto (S3, Supabase Storage) com bucket privado;
- nome final gerado no servidor: `crypto.randomUUID()` + extensão validada;
- caminho começando pelo `company_id` para permitir policy de isolamento;
- permissão de arquivo sem bit de execução;
- guardar hash (SHA-256) para deduplicação e para detectar alteração;
- quota por tenant, para upload não virar custo/DoS.

---

## 4. Servir arquivos

- URL assinada com validade curta para conteúdo privado; nunca URL previsível;
- `Content-Type` explícito e coerente; `X-Content-Type-Options: nosniff`;
- `Content-Disposition: attachment` para tipo que o navegador executaria;
- servir de domínio/subdomínio separado do app quando houver conteúdo do usuário (isola cookie e escopo de XSS);
- para download por ID: buscar o caminho no banco pelo ID **com filtro de tenant** — nunca aceitar caminho do cliente;
- validar path traversal quando o filesystem estiver envolvido (ver [owasp-vulnerabilidades.md](owasp-vulnerabilidades.md#path-traversal)).

---

## 5. Tipos com risco específico

| Tipo | Risco | Mitigação |
| --- | --- | --- |
| **SVG** | é XML: `<script>`, `<foreignObject>`, `xlink:href="javascript:"`, XXE | não aceitar; se aceitar, sanitizar com DOMPurify em modo SVG e servir como `attachment` de outro domínio |
| **HTML/HTM/XHTML** | XSS armazenado no seu domínio | não aceitar; se aceitar, servir de domínio isolado como download |
| **PDF** | JavaScript embutido, links, exploits de leitor | servir como download, não renderizar inline em domínio sensível |
| **Imagem** | polyglot com payload; exploit de biblioteca; bomba de pixels | reencode com biblioteca mantida, limitar dimensões e área total antes de decodificar |
| **ZIP/RAR** | zip slip (`../` na entrada), decompression bomb | validar cada entrada, limitar tamanho descomprimido e razão de compressão, ignorar link simbólico |
| **XML** | XXE, billion laughs | desativar entidades externas e DTD |
| **CSV** | CSV injection (`=`, `+`, `-`, `@`, tab, CR no início da célula) | prefixar com `'` ao exportar; validar ao importar |
| **Office/macro** | macro maliciosa | tratar como download; antivírus |
| **Executáveis** | óbvio | recusar |

### Bomba de pixels

`100000x100000` pixels em PNG de poucos KB estoura a memória ao decodificar. Ler dimensões pelo cabeçalho e recusar acima do limite antes de processar.

---

## 6. Processamento

- rodar conversão/thumbnail em worker isolado, com limite de CPU, memória e tempo;
- nunca passar nome de arquivo do usuário para linha de comando (`convert`, `ffmpeg`) via shell — array de argumentos e caminho gerado pelo servidor;
- ImageMagick/ffmpeg têm histórico de CVEs: manter atualizado, restringir formatos (policy.xml), desativar delegates desnecessários;
- remover metadados EXIF de foto pública (pode conter GPS e nome do dispositivo);
- não seguir referência remota dentro do arquivo (SSRF por SVG/XML/PDF).

---

## 7. Download / importação por URL

Quando o sistema busca arquivo a partir de URL do usuário, é SSRF. Aplicar as camadas de [owasp-vulnerabilidades.md](owasp-vulnerabilidades.md#ssrf): allowlist, resolução de IP, bloqueio de rede interna, sem redirect automático, timeout e limite de tamanho.

---

## 8. Revisão rápida

- [ ] upload exige autenticação e autorização
- [ ] tamanho máximo aplicado antes de bufferizar
- [ ] allowlist de extensão e tipo real por magic number
- [ ] nome gerado no servidor; original só como metadado
- [ ] storage sem execução, bucket privado, caminho com tenant
- [ ] SVG/HTML tratados ou recusados
- [ ] zip validado contra slip e bomba
- [ ] imagem reencodada, dimensões limitadas, EXIF removido quando público
- [ ] download resolve caminho pelo banco, com filtro de tenant
- [ ] URL assinada de validade curta para conteúdo privado
- [ ] `nosniff` + `Content-Type` fixo + `attachment` quando necessário
- [ ] quota e rate limit por tenant
- [ ] auditoria do envio
