# 🎨 FASE 2 — DESIGN DE AGENTES

Com base nas descobertas da análise do projeto, identificamos **7 domínios críticos** que exigem especialistas + 1 orquestrador.

## 🎯 Domínios Identificados

1. **🏗️ Backend/API** - Controllers, Models, Business Logic, Eloquent
2. **🗄️ Database** - Migrations, Schema, Queries, Optimization
3. **🧪 Testing** - Pest, Feature Tests, Unit Tests, TDD
4. **🎨 Frontend** - Blade, Vite, Tailwind, JavaScript
5. **🔐 Security** - Auth, Validation, Sanitization, Policies
6. **⚙️ DevOps** - Sail, Docker, Deploy, CI/CD, Performance
7. **📚 Documentation** - Code docs, API docs, Architecture docs

## 💡 Princípios de Design da Equipe

### ✅ Especialização Profunda
Cada agente é um verdadeiro expert em seu domínio, não um generalista.

### ✅ Colaboração Definida
As interações entre agentes são explicitamente mapeadas e estruturadas.

### ✅ Validação Cruzada
Quando domínios se sobrepõem, múltiplos especialistas validam o resultado.

### ✅ Orquestração Centralizada
Toda demanda passa primeiro pelo Orquestrador que roteia para os especialistas.

### ✅ Zero Generalização
Nenhum agente tenta fazer "um pouco de tudo" - cada um tem escopo claro.

## 🤖 Equipe de Agentes

### Agente 0: Orquestrador (Maestro)
**Responsabilidade**: Receber demandas, analisar, rotear para especialistas, coordenar colaboração

### Agente 1: Backend Architect
**Responsabilidade**: Controllers, Models, Services, Business Logic, Eloquent, API Design

### Agente 2: Database Specialist
**Responsabilidade**: Schema, Migrations, Query Optimization, Indexing, Performance

### Agente 3: Testing Specialist (QA)
**Responsabilidade**: Pest, Feature Tests, Unit Tests, TDD, Coverage, Mocking

### Agente 4: Frontend Specialist
**Responsabilidade**: Blade, Vite, Tailwind CSS, JavaScript, UX, Responsividade

### Agente 5: Security Specialist
**Responsabilidade**: Auth, Authorization, Validation, Sanitization, OWASP, Security Review

### Agente 6: DevOps Engineer
**Responsabilidade**: Docker/Sail, CI/CD, Deploy, Performance, Monitoring, Scaling

### Agente 7: Documentation Specialist
**Responsabilidade**: Code Docs, API Docs, Architecture Docs, README, Guides

## 🔄 Matriz de Colaboração Típica

### Nova Feature API Completa
```
Orquestrador → Backend Architect + Database Specialist + Security Specialist + Testing Specialist
```

### Alteração de UI
```
Orquestrador → Frontend Specialist + Backend Architect (se dados novos) + Security Specialist (se formulários)
```

### Otimização de Performance
```
Orquestrador → DevOps Engineer + Database Specialist + Backend Architect
```

### Deploy para Produção
```
Orquestrador → DevOps Engineer + Testing Specialist + Documentation Specialist
```

### Review de Segurança
```
Orquestrador → Security Specialist + Backend Architect + Database Specialist
```

## 📐 Arquitetura de Decisão

### Análise de Demanda
O Orquestrador usa os seguintes critérios:

1. **Palavras-chave**: identificar termos técnicos que indicam domínio
2. **Escopo**: single vs multi-domínio
3. **Complexidade**: simples (1 agente) vs complexa (2+ agentes)
4. **Impacto**: front-end, back-end, infra, segurança
5. **Fase**: desenvolvimento, teste, deploy, documentação

### Protocolo de Handoff

```
1. Orquestrador recebe demanda
2. Analisa e identifica domínio(s)
3. Aciona especialista(s) com contexto completo
4. Especialista(s) executam
5. Validação cruzada (se multi-domínio)
6. Orquestrador valida resultado final
7. Entrega ao usuário
```

## 🎭 Papéis e Responsabilidades

### O Orquestrador NUNCA:
- ❌ Implementa código diretamente
- ❌ Toma decisões técnicas sobre implementação
- ❌ Substitui especialistas

### O Orquestrador SEMPRE:
- ✅ Analisa a demanda primeiro
- ✅ Roteia para especialista(s) apropriado(s)
- ✅ Coordena colaboração entre especialistas
- ✅ Valida completude antes de entregar

### Especialistas NUNCA:
- ❌ Trabalham fora do seu domínio
- ❌ Tomam decisões sobre outros domínios sem consultar

### Especialistas SEMPRE:
- ✅ Seguem padrões e melhores práticas do seu domínio
- ✅ Colaboram quando solicitado
- ✅ Validam trabalho de outros especialistas em sobreposições
- ✅ Documentam decisões importantes

## 📊 Métricas de Sucesso

### Para o Sistema
- ✅ 100% das demandas roteadas para especialista correto
- ✅ Zero conflitos entre especialistas
- ✅ Todas as entregas validadas antes de finalizar
- ✅ Documentação sempre atualizada

### Para Especialistas
- ✅ Código segue padrões do domínio
- ✅ Testes escritos para toda funcionalidade
- ✅ Segurança verificada em todas as mudanças
- ✅ Performance considerada em implementações

## 🚀 Evolução do Sistema

### Quando Adicionar Novo Agente
- Novo domínio técnico significativo emerge
- Especialização atual não é suficiente
- Volume de demandas em sub-área justifica dedicação

### Quando Refinar Agente Existente
- Novas tecnologias adicionadas ao projeto
- Padrões evoluem ou mudam
- Feedback indica gap de conhecimento

---

**Versão**: 1.0.0
**Data**: 2026-02-23
