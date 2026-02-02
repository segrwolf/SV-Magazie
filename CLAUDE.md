# SV-Magazie

## Project Overview
Website for an elite print magazine. A premium presentation site showcasing the publication, attracting advertisers and participants.

- **Type**: Multi-page marketing/presentation website
- **Language**: English only (for now)
- **Target audience**: Advertisers, potential participants, readers
- **Current Domain**: https://svlifegroup.com (as of 2026-02-02)
- **Previous Domain**: https://svateliermagazine.com (301 redirects to new domain)

## Site Structure

### 1. Home (/)
- Hero section: photo of Sergey Vorontsov (main-photo.JPG)
- Slogan: *"This is not a magazine. Not a print edition. This is a new moodboard of life. Enjoy."*
- Author signature: **Serg Vorontsov**
- Magazine presentation block: brief intro to the publication
- Call-to-action buttons

### 2. About (/about)
- Mission statement
- Who it's for
- Core values: Unite, Advertise, Support
- History / vision of the project

### 3. Our Magazines (/magazines)
- Gallery/list of all published issues (newest first)
- Each issue: cover image, title, date, description
- PDF download/viewer for each issue
- Source PDFs (from svatelier.org, newest first):
  1. SV Atelier Magazine Issue 8 (2026) — https://svatelier.org/wp-content/uploads/2026/01/SVAtelierMagazine8-2026.pdf
  2. SV Atelier Magazine 2025 Preview — https://svatelier.org/wp-content/uploads/2025/03/SV-Atelier-–-Magazine-2025-Preview.pdf
  3. SV Atelier Magazine (2024) — https://svatelier.org/wp-content/uploads/2024/11/SV-Atelier2024.pdf
  4. SV Atelier Magazine Issue 6 (2024) — https://svatelier.org/wp-content/uploads/2024/04/SVAtelierMagazine6.pdf
  5. SV Atelier Magazine (October 2023) — https://svatelier.org/wp-content/uploads/SVmagazineoct23.pdf
  6. SV Atelier Magazine Preview 2 (2022) — https://svatelier.org/wp-content/uploads/2022/09/SV-Atelier-–-Magazine-Preview-2.pdf
  7. SV Atelier Magazine Issue 2 (2021) — https://svatelier.org/wp-content/uploads/2021/12/SV-Final-Preview.pdf
  8. SV Atelier Magazine Issue 1 (2021) — https://svatelier.org/wp-content/uploads/2021/10/SV-Atelier-–-Magazine.pdf

### 4. For Advertisers (/advertisers)
- Advertising opportunities
- Benefits of advertising in the magazine
- Pricing / packages / conditions
- CTA: contact manager

### 5. For Participants (/participants)
- Participation conditions
- Packages available
- What's included
- CTA: contact manager

### 6. Contacts (/contacts)
- Phone: +48 731 459 516
- Email: svatelierpoland@gmail.com
- Instagram: https://www.instagram.com/_sv_magazine_
- Contact form

### Global Elements
- **Header**: Logo (pictures/logo.png), navigation menu
- **Footer**: Quick links, social media, copyright
- **Sticky CTA button**: "Contact Manager" — opens a lead capture form (name, email, phone, message)

## Localization
- English only (for now, Russian may be added later)

## Tech Stack
- **Frontend**: HTML5, CSS3, vanilla JavaScript
- **Backend**: PHP (minimal, for admin panel + contact form)
- **Data storage**: JSON files (contacts.json, magazines.json) — no database needed
- **Hosting**: Shared hosting (PHP support required)
- **Admin panel**: Simple password-protected PHP page to:
  - Edit contact info (phone, email, social links)
  - Add/remove/reorder magazine issues (title, cover image, PDF link)
- **AI-friendly**: Clean, readable code structure — easy to modify via AI tools

## Project Structure
```
SV-Magazie/
├── CLAUDE.md
├── main-photo.JPG              # Photo of Sergey Vorontsov
├── pictures/
│   └── logo.png                # Project logo
├── index.html                  # Home page
├── about.html                  # About page
├── magazines.html              # Our Magazines page
├── advertisers.html            # For Advertisers page
├── participants.html           # For Participants page
├── contacts.html               # Contacts page
├── css/
│   └── style.css               # Main stylesheet
├── js/
│   └── main.js                 # Frontend scripts (modals, forms)
├── data/
│   ├── contacts.json           # Contact info (editable via admin)
│   └── magazines.json          # Magazine list (editable via admin)
├── admin/
│   ├── index.php               # Admin panel (password-protected)
│   ├── save-contacts.php       # API: save contacts
│   └── save-magazines.php      # API: save magazines
├── api/
│   └── contact-form.php        # Contact form handler (sends email)
└── assets/
    └── covers/                 # Magazine cover images
```

## Design Requirements
Reference site: https://svatelier.org (match this style)

- **Color palette**: White/light backgrounds, dark text (#000), secondary gray (#8c8c8c), accent teal (#00b7cd), warm taupe (#c2a593)
- **Typography**: Montserrat + Acme (Google Fonts), weights 400-700
- **Layout**: Max-width ~1200px, generous whitespace (15-30px spacing)
- **Aesthetic**: Premium, elegant, minimalist — luxury publication feel
- **Interactions**: Subtle fade effects, smooth transitions
- **Responsive**: Fully adaptive for all devices:
  - Desktop (1200px+)
  - Laptop (1024px)
  - Tablet (768px)
  - Mobile (375px+)
- High-quality imagery focus

## Functional Requirements
- PDF viewer/download for magazine issues
- Lead capture form ("Contact Manager" button)
- English only (for now)
- SEO-optimized pages
- Fast loading, optimized images

## Assets
- **Photo of Sergey Vorontsov**: `main-photo.JPG` (project root)
- **Logo**: `pictures/logo.png`
- **Magazine PDFs**: 8 issues, linked from svatelier.org (see Site Structure > Magazines)

## Notes
- All magazine issues to be stored as PDF files

---

## Project History & Current Status

### Branding Evolution
- **Original Name**: SV Magazine
- **Current Brand**: SV Life / SV Life Media Group
- **Date Changed**: 2026-02-02

### Domain Migration
- **Old Domain**: svateliermagazine.com (301 redirects to new domain)
- **Current Domain**: svlifegroup.com (live since 2026-02-02)
- **Hosting**: Server 91.234.33.250, user: seoport7
- **Web Root**: /www/svlifegroup.com

### Latest Updates (2026-02-02)
1. Added new magazine issue: SV Magazine — 2026 Print Edition
2. Added "Mission of SV Life" section to About page
3. Rebranded from "SV Magazine" to "SV Life" across all pages
4. Updated footer branding to "SV Life Media Group"
5. Migrated website to new domain svlifegroup.com
6. Set up 301 redirect from old domain

### Magazine Catalog
Current count: **9 issues** (2021-2026)
- Latest: SV Magazine — 2026 Print Edition (January 2026)
- All PDFs hosted on svatelier.org
- Covers stored as external URLs

### Key Contacts
- Phone: +48 731 459 516
- Email: svatelierpoland@gmail.com
- Instagram: @_sv_magazine_

### Technical Stack
- Static HTML/CSS/JavaScript
- PHP backend for forms and admin
- JSON data storage (no database)
- Admin panel: /svadministratormain/

### Deployment
- **Primary Domain**: https://svlifegroup.com
- **Old Domain Redirect**: https://svateliermagazine.com → https://svlifegroup.com
- **FTP Access**: 91.234.33.250
- **Last Deployed**: 2026-02-02

### reCAPTCHA Configuration
- **Site Key**: 6LcTFF4sAAAAAJIRnKab5KMbMKkVsVolFFVcY0V3
- **Secret Key**: 6LcTFF4sAAAAAB4eklCRX-muT9I_s0RVLaBeHIsp
- **Domain**: svlifegroup.com
- **Updated**: 2026-02-02
