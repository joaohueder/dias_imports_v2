# Supply chain e CI/CD

O código que você não escreveu roda com o mesmo privilégio do que você escreveu.

---

## 1. Antes de adicionar uma dependência

| Critério | Pergunta |
| --- | --- |
| Necessidade | resolve algo real ou substitui 10 linhas? |
| Manutenção | último release recente? issues respondidas? |
| Reputação | downloads, mantenedores, projeto com histórico |
| Vulnerabilidades | CVE/advisory abertos? |
| Transitivas | quantas dependências ela arrasta? |
| Tamanho | impacto no bundle e na superfície |
| Licença | compatível com o projeto |
| Alternativa nativa | a plataforma já faz isso? (`crypto`, `Intl`, `URL`, `structuredClone`) |

> Não adicionar biblioteca para resolver poucas linhas quando isso amplia a superfície de ataque.

### Typosquatting

Conferir o nome caractere por caractere antes de instalar. Padrões suspeitos: hífen a mais/menos, letra trocada (`reqeust`, `crossenv`, `lodahs`), escopo falso (`@types-node`), pacote novo com nome parecido e muitos downloads súbitos.

Verificar também: repositório declarado existe e corresponde? A versão publicada tem release no GitHub? Há `postinstall` script?

### Slopsquatting

Nome de pacote sugerido por IA pode não existir — e alguém pode ter registrado exatamente aquele nome. Confirmar existência e legitimidade no registro oficial antes de instalar qualquer pacote sugerido.

---

## 2. Lockfile e instalação

- lockfile **sempre** versionado (`package-lock.json`, `pnpm-lock.yaml`, `yarn.lock`);
- CI usa `npm ci` (instalação determinística), nunca `npm install`;
- `--ignore-scripts` quando viável, principalmente em CI — `postinstall` é vetor de execução arbitrária;
- fixar versão exata para dependência sensível (crypto, auth, parser);
- revisar diff do lockfile em PR: mudança inesperada de resolução ou de registry é sinal de alerta;
- registry único e confiável; cuidado com dependency confusion em pacote interno (usar escopo próprio e bloquear fallback para o registro público).

---

## 3. Auditoria de dependências

```bash
npm audit --omit=dev            # produção primeiro
npm audit fix                   # só o que não quebra
npm outdated
```

Priorizar por:

```text
1. criticidade da vulnerabilidade
2. possibilidade REAL de exploração no seu uso
3. exposição (roda no servidor? recebe input externo?)
4. impacto
```

`npm audit` reporta advisory transitivo que muitas vezes não é alcançável pelo seu código (ex.: só no build). **Não atualizar dezenas de pacotes cegamente** — avaliar breaking change, testar, e registrar decisão quando escolher conviver com o risco.

Ferramentas complementares: Dependabot/Renovate para PRs automáticos, `osv-scanner`, Trivy para imagem, SBOM (CycloneDX/SPDX) quando o cliente exigir.

---

## 4. CI/CD

### Secrets

- em store de secrets da plataforma, nunca no YAML nem no repositório;
- escopo mínimo: por ambiente, por job;
- **não imprimir**: cuidado com `set -x`, `echo $TOKEN`, `env` no log, e com secret passado por linha de comando (aparece na lista de processos);
- mascaramento não é garantia: secret transformado (base64, JSON) escapa do mascaramento;
- secret de produção não disponível para workflow disparado por fork/PR externo;
- rotação periódica e revogação ao trocar de fornecedor ou desligar pessoa.

### Permissões

```yaml
permissions:
  contents: read        # deny by default; elevar só onde precisa
```

- token do runner com permissão mínima; `id-token: write` só no job que faz OIDC;
- preferir **OIDC federado** a chave de longa duração para deploy em cloud;
- ambiente de produção com aprovação manual e branch protegida;
- `pull_request_target` é perigoso: executa com secrets no contexto do PR externo. Evitar ou isolar rigorosamente.

### Actions e imagens de terceiros

- fixar por **commit SHA**, não por tag (`uses: acme/action@<sha>`) — tag é mutável;
- preferir actions oficiais e revisar o código do que for de terceiro;
- runner self-hosted não deve executar PR de fork (execução arbitrária no seu ambiente);
- cache não deve guardar secret nem artefato sensível; cache é compartilhado entre branches.

### Pipeline de segurança recomendado

```text
lint → typecheck → testes → testes de autorização/tenant
→ secret scanning (gitleaks/trufflehog) → npm audit
→ SAST → build → scan da imagem → deploy com OIDC
```

Falha de secret scanning bloqueia o merge.

---

## 5. Git

Nunca versionar: `.env`, `.env.*` (exceto `.env.example` sem valores), dump SQL, backup, `*.pem`, `*.key`, `*.p12`, `*.pfx`, `id_rsa`, `credentials.json`, `serviceAccount.json`, `*.keystore`, arquivo de sessão, cookie exportado.

`.gitignore` de partida:

```gitignore
.env
.env.*
!.env.example
*.pem
*.key
*.p12
*.pfx
*.sql.gz
*.dump
credentials*.json
serviceAccount*.json
node_modules/
dist/
coverage/
```

- pre-commit hook com secret scanning (não usar `--no-verify` para contornar);
- histórico: secret commitado **continua no histórico**. Remover o arquivo não resolve → rotacionar a credencial (ver [secrets-credenciais.md](secrets-credenciais.md));
- branch protegida em `main`: PR obrigatório, review, checks verdes, sem force push;
- assinatura de commit quando o projeto exigir procedência.

---

## 6. Revisão rápida

- [ ] toda dependência nova justificada e verificada (nome, manutenção, CVE)
- [ ] lockfile versionado e `npm ci` no CI
- [ ] scripts de instalação avaliados
- [ ] `npm audit` de produção sem crítico/alto pendente sem decisão registrada
- [ ] actions fixadas por SHA
- [ ] `permissions` mínimo no workflow
- [ ] secrets fora do YAML e ausentes do log
- [ ] deploy por OIDC ou credencial de escopo mínimo
- [ ] secret scanning no pipeline e no pre-commit
- [ ] `.gitignore` cobrindo env, chaves e dumps
- [ ] branch de produção protegida
