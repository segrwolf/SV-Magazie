# ТЗ на SEO-исправления — dateromania.com

**Для:** Claude Code (coding agent)
**Проект:** Nuxt 3 приложение, TailwindCSS, i18n (`@nuxtjs/i18n`)
**Сервер:** VPS, `/var/www/dateromania.com`
**Дата:** 2026-02-01

---

## НЕДЕЛЯ 1 — Критичные исправления

### 1.1 Добавить hreflang теги на все страницы

**Сейчас:** hreflang отсутствует полностью
**Нужно:** На каждой странице в `<head>` — hreflang для всех 3 языков + x-default

**Как исправить:**

В Nuxt 3 с `@nuxtjs/i18n` — включить в `nuxt.config.ts`:

```ts
// nuxt.config.ts
i18n: {
  baseUrl: 'https://dateromania.com',
  // ... остальная конфигурация
  locales: [
    { code: 'ro', language: 'ro-RO', ... },
    { code: 'en', language: 'en-US', ... },
    { code: 'ru', language: 'ru-RU', ... },
  ],
}
```

Если `@nuxtjs/i18n` не генерирует hreflang автоматически, добавить через `useHead()` в `app.vue` или layout:

```ts
// layouts/default.vue или app.vue
const { locale, locales } = useI18n()
const route = useRoute()
const switchLocalePath = useSwitchLocalePath()

useHead(() => {
  const links = locales.value.map(loc => ({
    rel: 'alternate',
    hreflang: loc.language || loc.code,
    href: `https://dateromania.com${switchLocalePath(loc.code)}`,
  }))

  links.push({
    rel: 'alternate',
    hreflang: 'x-default',
    href: `https://dateromania.com${switchLocalePath('ro')}`,
  })

  return { link: links }
})
```

**Результат в HTML:**
```html
<link rel="alternate" hreflang="ro-RO" href="https://dateromania.com/populatie">
<link rel="alternate" hreflang="en-US" href="https://dateromania.com/en/population">
<link rel="alternate" hreflang="ru-RU" href="https://dateromania.com/ru/naselenie">
<link rel="alternate" hreflang="x-default" href="https://dateromania.com/populatie">
```

---

### 1.2 Добавить canonical теги на все страницы

**Сейчас:** canonical отсутствует
**Нужно:** `<link rel="canonical">` на каждой странице

**Как исправить:**

В layout или app.vue:

```ts
// layouts/default.vue
const route = useRoute()
const runtimeConfig = useRuntimeConfig()

useHead(() => ({
  link: [
    {
      rel: 'canonical',
      href: `${runtimeConfig.public.siteUrl}${route.path}`,
    },
  ],
}))
```

**Важно:** Canonical должен указывать на текущую языковую версию, НЕ на основной язык.

---

### 1.3 Настроить редирект www → non-www

**Сейчас:** `https://www.dateromania.com` отдаёт 200 (дубль!)
**Нужно:** 301 редирект на `https://dateromania.com`

**Как исправить:**

Если Nginx:
```nginx
# /etc/nginx/sites-available/dateromania.com
server {
    listen 443 ssl;
    server_name www.dateromania.com;

    ssl_certificate     /path/to/cert;
    ssl_certificate_key /path/to/key;

    return 301 https://dateromania.com$request_uri;
}
```

Если используется Nuxt server middleware:
```ts
// server/middleware/redirect-www.ts
export default defineEventHandler((event) => {
  const host = getRequestHost(event)
  if (host?.startsWith('www.')) {
    const url = getRequestURL(event)
    return sendRedirect(event, `https://dateromania.com${url.pathname}${url.search}`, 301)
  }
})
```

---

### 1.4 Исправить 404 URL в sitemap

**Сейчас:** sitemap-main.xml содержит URL которые отдают 404:
- `/en/confidentialitate` → 404 (реальный URL: `/en/privacy`)
- `/ru/confidentialitate` → 404 (реальный URL: `/ru/privacy`)
- `/en/termeni` → 404 (реальный URL: `/en/terms`)
- `/ru/termeni` → 404 (реальный URL: `/ru/terms`)

**Нужно:** Или исправить URL в sitemap, или создать роуты/редиректы.

**Вариант А — исправить sitemap:**

Найти файл генерации sitemap (вероятно `server/routes/sitemap-main.xml.ts` или аналогичный) и заменить:

```diff
- { loc: 'https://dateromania.com/en/confidentialitate' }
+ { loc: 'https://dateromania.com/en/privacy' }

- { loc: 'https://dateromania.com/ru/confidentialitate' }
+ { loc: 'https://dateromania.com/ru/privacy' }

- { loc: 'https://dateromania.com/en/termeni' }
+ { loc: 'https://dateromania.com/en/terms' }

- { loc: 'https://dateromania.com/ru/termeni' }
+ { loc: 'https://dateromania.com/ru/terms' }
```

**Вариант Б — добавить редиректы** (если хочется сохранить оба URL):
```ts
// nuxt.config.ts
routeRules: {
  '/en/confidentialitate': { redirect: '/en/privacy' },
  '/ru/confidentialitate': { redirect: '/ru/privacy' },
  '/en/termeni': { redirect: '/en/terms' },
  '/ru/termeni': { redirect: '/ru/terms' },
}
```

Также `/confidentialitate` (RO) редиректит на `/en/privacy` — это баг, должен оставаться на RO версии.

---

### 1.5 Добавить полные Open Graph теги

**Сейчас:** Только og:type и og:site_name
**Нужно:** Полный набор OG + Twitter Cards

**Как исправить:**

Создать composable:

```ts
// composables/useSeoMeta.ts
export function usePageSeo(options: {
  title: string
  description: string
  path: string
  image?: string
  locale?: string
}) {
  const config = useRuntimeConfig()
  const url = `${config.public.siteUrl}${options.path}`
  const image = options.image || `${config.public.siteUrl}/og-default.png`

  useSeoMeta({
    ogTitle: options.title,
    ogDescription: options.description,
    ogUrl: url,
    ogImage: image,
    ogLocale: options.locale || 'ro_RO',
    twitterCard: 'summary_large_image',
    twitterTitle: options.title,
    twitterDescription: options.description,
    twitterImage: image,
  })
}
```

**Также нужно:**
1. Создать OG-изображение: `public/og-default.png` (1200x630px) — карта Румынии с логотипом
2. Для каждого раздела можно создать отдельное OG-изображение

---

## НЕДЕЛЯ 2 — Важные исправления

### 2.1 Оптимизировать title теги

**Сейчас → Нужно:**

| Страница | Сейчас | Нужно |
|----------|--------|-------|
| /en/population | "Population in Romania" | "Population of Romania 2024 — Statistics by County \| Date Romania" |
| /en/salaries | "Salaries in Romania" | "Average Salaries in Romania 2024 — By County \| Date Romania" |
| /en/crime | "Crime in Romania" (предположительно) | "Crime Statistics in Romania 2024 — By County \| Date Romania" |
| /en/unemployment | "Unemployment in Romania" (предположительно) | "Unemployment Rate in Romania 2024 — By County \| Date Romania" |
| /en/contact | "Contact - Date Romania" | "Contact Us — Date Romania Statistics Portal" |

**Как:** В каждом page component обновить `useHead()`:

```ts
// pages/en/population.vue (или аналогичный)
useHead({
  title: `Population of Romania ${year} — Statistics by County | Date Romania`,
  meta: [
    { name: 'description', content: `Romania population ${year}: ${total.toLocaleString()} inhabitants. Demographic statistics by county and region. Data from INS.` }
  ]
})
```

**Формат title:** `{Тема} {Год} — {Дополнение} | Date Romania`

---

### 2.2 Убрать дублирование inline CSS

**Сейчас:** Tailwind CSS вставляется inline ~2 раза в `<head>` (полный сброс + утилиты) = ~80-100KB
**Нужно:** Один внешний CSS файл (Nuxt и так генерирует `/_nuxt/default.*.css`)

**Как исправить:**

Проверить `nuxt.config.ts`:
```ts
// Убедиться что CSS не инлайнится дважды
css: [],
postcss: {
  plugins: {
    tailwindcss: {},
    autoprefixer: {},
  }
}
```

Возможно проблема в двойном подключении — через `@nuxtjs/tailwindcss` модуль и ручной inline. Нужно проверить:
1. Есть ли `@nuxtjs/tailwindcss` в modules
2. Нет ли ручного `<style>` в `app.vue` с дублированием

---

### 2.3 Условная загрузка Leaflet CSS

**Сейчас:** `<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">` на ВСЕХ страницах
**Нужно:** Только на страницах с картой

**Как исправить:**

Вместо глобального подключения в `nuxt.config.ts`:
```ts
// УБРАТЬ из nuxt.config.ts
app: {
  head: {
    link: [
      // УДАЛИТЬ эту строку:
      // { rel: 'stylesheet', href: 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css' }
    ]
  }
}
```

Подключать в компоненте карты:
```ts
// components/RomaniaMap.vue
useHead({
  link: [
    { rel: 'stylesheet', href: 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css' }
  ]
})
```

---

### 2.4 Исправить баг с unhead payload

**Сейчас:** `<script id="unhead:payload">{"title":"Date Romania - Statistici Oficiale"}</script>` присутствует на ВСЕХ страницах, включая EN и RU
**Нужно:** Payload должен соответствовать текущему языку

**Как:** Проверить, не задан ли title статически в `nuxt.config.ts`:

```ts
// nuxt.config.ts — найти и изменить:
app: {
  head: {
    // УБРАТЬ статический title, если он тут:
    // title: 'Date Romania - Statistici Oficiale'
  }
}
```

Title должен задаваться динамически через `useHead()` в layout или i18n.

---

## МЕСЯЦ 1 — Рекомендации

### 3.1 Создать раздел блога

**Зачем:** Long-tail трафик, ссылочная масса, регулярный контент
**Темы:**
- "Топ-10 жудецов по зарплате в 2024"
- "Как менялась численность населения Румынии"
- "Сравнение зарплат в Румынии и других странах ЕС"

**Как:** Создать роут `/blog` (→ `/en/blog`, `/ru/blog`) с markdown или CMS.

---

### 3.2 Добавить Cookie Consent Banner

**Сейчас:** Не обнаружен
**Нужно:** GDPR-совместимый баннер (есть GA + AdSense)

**Как:** Установить модуль `@dargmuesli/nuxt-cookie-control` или аналог:

```bash
npm install @dargmuesli/nuxt-cookie-control
```

```ts
// nuxt.config.ts
modules: ['@dargmuesli/nuxt-cookie-control'],
cookieControl: {
  cookies: {
    necessary: [{ name: 'i18n_locale', ... }],
    optional: [
      { name: 'Google Analytics', id: 'ga', ... },
      { name: 'Google AdSense', id: 'ads', ... },
    ]
  }
}
```

---

### 3.3 Добавить og:locale:alternate теги

**Для мультиязычного OG:**

```html
<meta property="og:locale" content="en_US">
<meta property="og:locale:alternate" content="ro_RO">
<meta property="og:locale:alternate" content="ru_RU">
```

Добавить в composable `usePageSeo()` из п.1.5.

---

### 3.4 Оптимизировать AI-генерированный контент

**Сейчас:** Narrative-тексты хранятся с метками `generatedAt` (видно в NUXT_DATA) — явно AI
**Нужно:**
1. Убрать `generatedAt` из клиентского payload (не нужен пользователю)
2. Добавить в тексты реальные цитаты, ссылки на источники
3. Разнообразить структуру текстов между разделами

---

### 3.5 Создать страницу /en/metodologie

**Сейчас:** Ссылка в footer на `/en/metodologie` — проверить что страница существует и содержит описание источников данных (INS, ANOFM, etc.), периодичность обновлений, методологию расчётов.

---

### 3.6 Добавить breadcrumbs в HTML

**Сейчас:** BreadcrumbList есть в JSON-LD, но визуальных хлебных крошек на странице нет
**Нужно:** Визуальные breadcrumbs для UX + дополнительная внутренняя перелинковка

```vue
<!-- components/Breadcrumbs.vue -->
<nav aria-label="Breadcrumb" class="text-sm text-gray-500 mb-4">
  <ol class="flex items-center gap-2">
    <li><a href="/en" class="hover:text-blue-600">Home</a></li>
    <li>/</li>
    <li><a href="/en/population" class="hover:text-blue-600">Population</a></li>
    <li>/</li>
    <li class="text-gray-900 font-medium">Bucharest</li>
  </ol>
</nav>
```

---

## Чек-лист приоритетов

| # | Задача | Приоритет | Сложность | Файлы |
|---|--------|-----------|-----------|-------|
| 1.1 | hreflang | 🔴 Критично | Средняя | nuxt.config.ts, layouts/default.vue |
| 1.2 | canonical | 🔴 Критично | Низкая | layouts/default.vue |
| 1.3 | www → non-www | 🔴 Критично | Низкая | nginx config |
| 1.4 | Sitemap 404 | 🔴 Критично | Низкая | server/routes/sitemap-main.xml.ts |
| 1.5 | OG теги | 🔴 Критично | Средняя | composables/, layouts/, public/ |
| 2.1 | Title оптимизация | 🟡 Важно | Низкая | pages/*.vue |
| 2.2 | CSS дубли | 🟡 Важно | Средняя | nuxt.config.ts, app.vue |
| 2.3 | Leaflet условный | 🟡 Важно | Низкая | nuxt.config.ts, components/RomaniaMap.vue |
| 2.4 | Unhead payload | 🟡 Важно | Низкая | nuxt.config.ts |
| 3.1 | Блог | 🟢 Рекомендация | Высокая | Новый модуль |
| 3.2 | Cookie consent | 🟢 Рекомендация | Средняя | nuxt.config.ts |
| 3.3 | og:locale | 🟢 Рекомендация | Низкая | composables/ |
| 3.4 | AI контент | 🟢 Рекомендация | Средняя | Бэкенд генерации |
| 3.5 | Методология | 🟢 Рекомендация | Низкая | pages/ |
| 3.6 | Breadcrumbs UI | 🟢 Рекомендация | Низкая | components/ |
