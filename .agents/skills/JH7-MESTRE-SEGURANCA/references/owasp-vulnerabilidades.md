# Catálogo de vulnerabilidades — detecção, exploração e correção

Referência de consulta durante revisão de código e auditoria. Cada item traz: como detectar no repositório, como seria explorado e qual é a correção.

---

## 1. Injection

### SQL Injection

**Detectar:** concatenação ou template string dentro de query; uso de `query(...)` com `+`, `${}`, `%s`, `.format()`; `WHERE` montado a partir de `req.query`; `order by` dinâmico; `LIKE` com input cru; `raw()`/`unsafe()` de ORM.

**Exploração:** `' OR 1=1 --`, `'; DROP TABLE users; --`, `UNION SELECT` para extrair dados de outras tabelas, `pg_sleep()` para blind timing.

**Correção:** parâmetros vinculados, sempre.

```javascript
// ❌
db.query(`select * from users where email = '${email}'`);

// ✅ Postgres (pg)
db.query('select * from users where email = $1', [email]);

// ✅ MySQL
db.execute('select * from users where email = ?', [email]);
```

Identificador dinâmico (nome de coluna/tabela) **não** aceita parâmetro — resolva por allowlist:

```javascript
const ORDER = { nome: 'name', data: 'created_at' };
const col = ORDER[req.query.sort] ?? 'created_at';
```

Em PL/pgSQL, `EXECUTE` só com `format(..., %I, %L)` ou `USING`.

### NoSQL Injection

**Detectar:** objeto do body indo direto para o filtro (`find(req.body)`), `$where`, operadores aceitos do cliente.

**Exploração:** `{"password": {"$ne": null}}` autentica sem senha.

**Correção:** coagir tipo (`String(x)`), validar por schema, rejeitar chaves que comecem com `$` ou contenham `.`.

### Command Injection

**Detectar:** `exec`, `execSync`, `system`, `shell_exec`, `popen`, `os.system`, backticks, `sh -c` com dado do usuário.

**Correção:** API nativa quando existir; senão, forma com array de argumentos e sem shell.

```javascript
// ❌
exec(`convert ${file} out.png`);

// ✅
execFile('convert', [file, 'out.png'], { shell: false });
```

Ainda valide `file` por allowlist — argumento que começa com `-` pode virar flag.

### Code Injection

**Detectar:** `eval`, `new Function`, `setTimeout('string')`, `vm.runInThisContext`, `require(variável)`, `import(variável)`, desserialização que instancia classes.

**Correção:** não avaliar string externa. Para expressão de usuário, usar parser restrito com allowlist de operações.

### Template Injection (SSTI)

**Detectar:** template compilado a partir de string do usuário (`Handlebars.compile(userInput)`, `ejs.render(userInput)`, f-string com dado externo em Jinja).

**Correção:** template é código — vem do repositório. Usuário fornece **dados** para o template, nunca o template.

### Header / Log Injection

**Detectar:** `\r\n` possível em valor de header, `Location`, `Set-Cookie`, nome de arquivo em `Content-Disposition`, e input cru em log.

**Correção:** remover CR/LF, usar API que codifica o header, log estruturado (JSON) em vez de concatenar texto.

---

## 2. Frontend

### XSS

| Tipo | Onde nasce |
| --- | --- |
| Stored | conteúdo salvo no banco e renderizado depois |
| Reflected | parâmetro da URL refletido na página |
| DOM | JavaScript escreve no DOM a partir de `location`, `postMessage`, etc. |

**Detectar:** `innerHTML`, `outerHTML`, `insertAdjacentHTML`, `document.write`, `dangerouslySetInnerHTML`, `v-html`, `[innerHTML]`, `eval`, `href={userInput}`, `<script>` com dado interpolado no servidor.

**Correção:**

- renderizar como **texto** (`textContent`, interpolação padrão de React/Vue/Angular já escapa);
- quando HTML rico for requisito, sanitizar com biblioteca mantida (DOMPurify) e allowlist de tags/atributos;
- URL: validar esquema — aceitar só `https:`, `http:`, `mailto:`; bloquear `javascript:`, `data:`, `vbscript:`;
- CSP como segunda camada, nunca como única.

```javascript
// ❌
el.innerHTML = produto.descricao;

// ✅
el.textContent = produto.descricao;
// ou, se HTML for necessário:
el.innerHTML = DOMPurify.sanitize(produto.descricao);
```

### CSRF

**Aplicável quando** a autenticação viaja em cookie enviado automaticamente. Com `Authorization: Bearer` lido de storage, o vetor clássico não existe (mas o XSS fica mais grave).

**Correção:** token anti-CSRF por sessão, `SameSite=Lax` ou `Strict`, verificar `Origin`/`Referer` em requisição que altera estado, e nunca aceitar `GET` para operação com efeito colateral.

### Clickjacking

**Correção:** `Content-Security-Policy: frame-ancestors 'self'` (e `X-Frame-Options: DENY` para compatibilidade).

### Open Redirect

**Detectar:** `?next=`, `?redirect=`, `?url=`, `?returnTo=` usados em `res.redirect` ou `location.href`.

**Correção:** allowlist de destinos, ou aceitar apenas caminho relativo que comece com `/` e não com `//` nem `/\`.

### Prototype pollution / DOM clobbering

**Detectar:** merge recursivo caseiro, `Object.assign` profundo, `lodash.merge` com body cru, `JSON.parse` seguido de merge; leitura de `window.<id>` que pode ser criado por `<a id=...>`.

**Correção:** rejeitar chaves `__proto__`, `constructor`, `prototype`; `Object.create(null)` para mapas; validar por schema antes de mesclar; nunca depender de global implícito do DOM.

---

## 3. Backend e API

### SSRF

**Detectar:** `fetch`/`axios`/`request`/`curl` com URL vinda do usuário — importação por URL, webhook configurável, preview de link, download de imagem, proxy.

**Exploração:** `http://169.254.169.254/latest/meta-data/` (credencial de cloud), `http://localhost:8000/admin`, `http://kong:8000`, `file:///etc/passwd`, redirect 302 para IP interno, DNS rebinding.

**Correção em camadas:**

1. allowlist de domínios quando possível;
2. esquema só `http`/`https`;
3. resolver o DNS, validar o IP resultante e **conectar nesse IP** (evita rebinding);
4. bloquear loopback, link-local (`169.254/16`), privados (`10/8`, `172.16/12`, `192.168/16`), `::1`, `fc00::/7`;
5. não seguir redirect automaticamente — revalidar cada salto;
6. timeout e limite de tamanho de resposta;
7. saída por proxy/rede segregada sem acesso a serviço interno;
8. nunca devolver o corpo bruto da resposta ao usuário.

### IDOR / BOLA / BFLA

**Detectar:** rota com `:id` que consulta direto pelo ID sem cláusula de propriedade; `findById(req.params.id)`; update/delete por ID; rota admin sem verificação de papel (BFLA).

**Correção:** a autorização entra na própria query.

```javascript
// ❌
const pedido = await Pedido.findById(id);

// ✅
const pedido = await Pedido.findOne({
  id,
  company_id: session.companyId, // vindo do servidor, não do body
});
if (!pedido) return notFound(); // 404, não 403 — evita enumeração
```

### Mass assignment

**Detectar:** `create(req.body)`, `update(req.body)`, `Object.assign(entity, req.body)`, `save({...req.body})`.

**Correção:** allowlist de campos, DTO validado por schema, e campos sensíveis (`role`, `company_id`, `is_admin`, `saldo`, `plano`, `email_verificado`) só alteráveis por fluxo próprio e autorizado.

### Path traversal

**Detectar:** `path.join(base, req.params.file)`, `readFile(userPath)`, download por nome, extração de zip.

**Correção:**

```javascript
const base = path.resolve('/app/uploads');
const alvo = path.resolve(base, path.basename(nome));
if (!alvo.startsWith(base + path.sep)) throw new Error('caminho inválido');
```

Prefira mapear ID → caminho pelo banco, sem aceitar caminho do cliente. Em zip, validar cada entrada (zip slip) e limitar razão de compressão.

### Deserialização insegura

**Detectar:** `pickle`, `yaml.load` sem `SafeLoader`, `unserialize` de PHP, `ObjectInputStream` de Java, `node-serialize`.

**Correção:** JSON com schema. Se objeto binário for inevitável, assinar com HMAC e validar antes de desserializar.

### Race condition / TOCTOU

**Detectar:** `if (saldo >= valor) { ... update }`, checar-depois-usar em cupom, estoque, limite de plano, resgate de convite, criação de registro único.

**Correção:** operação atômica (`update ... set saldo = saldo - $1 where saldo >= $1`), constraint `UNIQUE`, transação com `SELECT ... FOR UPDATE`, lock consultivo, chave de idempotência.

### Consumo ilimitado de recursos

**Detectar:** paginação sem limite máximo, `limit` do cliente sem teto, filtro que permite `ORDER BY` custoso, exportação total, geração de PDF/relatório sem fila, upload sem tamanho máximo, GraphQL sem profundidade limitada.

**Correção:** teto de página (ex.: 100), timeout de query, rate limit, fila para trabalho pesado, limite de profundidade/complexidade em GraphQL, `body-parser` com `limit`.

### Request smuggling / parameter pollution

**Detectar:** múltiplos proxies com interpretação divergente; leitura de parâmetro repetido (`?id=1&id=2`) sem definir precedência.

**Correção:** manter proxy e runtime atualizados, normalizar parâmetro (primeiro valor ou rejeitar duplicata), nunca confiar em header adicionado por proxy sem sanitizar na borda (`X-Forwarded-For`).

---

## 4. Autenticação (resumo)

Brute force, credential stuffing, password spraying, session fixation, hijacking, token replay, manipulação de JWT, enumeração de contas. Detalhamento em [autenticacao-autorizacao.md](autenticacao-autorizacao.md).

---

## 5. Arquivos

Upload irrestrito, MIME spoofing, SVG com script, decompression bomb, malware. Detalhamento em [upload-arquivos.md](upload-arquivos.md).

---

## Mapa rápido de busca no repositório

Termos que valem grep numa revisão:

```text
eval(  new Function(  exec(  execSync(  child_process
innerHTML  dangerouslySetInnerHTML  v-html  document.write
raw(  unsafe(  query(`  ${  + req.  + userId
findById(  req.body)  Object.assign(  merge(
path.join(  readFile(  createReadStream(
fetch(url  axios.get(url  http.get(
Access-Control-Allow-Origin  *
process.env  API_KEY  SECRET  TOKEN  PASSWORD  service_role
md5  sha1  Math.random(  jwt.decode(  verify: false
rejectUnauthorized: false  verify=False  --insecure
```
