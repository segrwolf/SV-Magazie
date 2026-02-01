# Deployment Instructions for SV-Magazie

## Files to Upload

### Required Files & Folders:
```
SV-Magazie/
├── index.html              ← Home page
├── about.html              ← About page
├── magazines.html          ← Magazines catalog
├── advertisers.html        ← Advertisers page
├── participants.html       ← Participants page
├── contacts.html           ← Contact page
├── css/
│   └── style.css          ← Main stylesheet
├── js/
│   └── main.js            ← JavaScript functionality
├── pictures/              ← All images
│   ├── logo.png
│   ├── svmediagrouplogo.png
│   ├── main-photo.jpg
│   ├── sergevorontsov2.jpg
│   ├── svpodpisnor.png
│   ├── background2.jpg
│   └── backgroud1.jpg
├── data/                  ← Data files
│   ├── contacts.json
│   ├── magazines.json
│   ├── leads.log
│   └── .htaccess
├── api/
│   └── contact-form.php   ← Contact form handler
└── svadministratormain/   ← Admin panel
    ├── index.php
    ├── save-contacts.php
    ├── save-magazines.php
    └── .htaccess
```

### DO NOT Upload:
- `.git/` folder
- `.claude/` folder
- `CLAUDE.md`
- `DEPLOY.md`
- `docs/` folder
- `.DS_Store` files
- `index.backup.html`

## Deployment Methods

### Option 1: FileZilla (Recommended for FTP/SFTP)

1. **Download FileZilla**: https://filezilla-project.org/download.php?type=client

2. **Connect to your hosting**:
   - Host: `ftp.yourdomain.com` or `sftp.yourdomain.com`
   - Username: `your_username`
   - Password: `your_password`
   - Port: `21` (FTP) or `22` (SFTP)

3. **Upload files**:
   - Navigate to your web root (usually `/public_html` or `/www` or `/httpdocs`)
   - Drag and drop all files/folders from the list above
   - Make sure folder structure is preserved

4. **Set permissions** (important for PHP files):
   ```
   Folders: 755
   PHP files: 644
   .htaccess: 644
   data/ folder: 755 (must be writable)
   ```

### Option 2: Command Line (SFTP)

If you have SSH access, you can use command line:

```bash
# Connect via SFTP
sftp username@yourdomain.com

# Navigate to web root
cd public_html

# Upload files
put -r css
put -r js
put -r pictures
put -r data
put -r api
put -r svadministratormain
put *.html

# Set permissions
chmod 755 data
chmod 644 data/contacts.json
chmod 644 data/magazines.json
```

### Option 3: rsync (Fastest for SSH)

```bash
# From your local project folder, run:
rsync -avz --exclude='.git' --exclude='.claude' --exclude='*.md' --exclude='docs' \
  ./ username@yourdomain.com:/path/to/public_html/
```

## Post-Deployment Checklist

1. **Test the website**:
   - [ ] Visit https://yourdomain.com
   - [ ] Check all pages load correctly
   - [ ] Test navigation menu
   - [ ] Test contact form
   - [ ] Test modal popup
   - [ ] Check mobile responsiveness

2. **Verify PHP functionality**:
   - [ ] Contact form sends emails
   - [ ] Admin panel is accessible at `/svadministratormain/`
   - [ ] Admin panel password protection works

3. **Configure email** (for contact form):
   - Edit `api/contact-form.php`
   - Set your email address in line with `mail()` function

4. **Security**:
   - [ ] Change admin panel password in `/svadministratormain/index.php`
   - [ ] Ensure `data/` folder is protected (`.htaccess` blocks direct access)
   - [ ] Test reCAPTCHA on contact form

5. **Performance**:
   - [ ] Enable gzip compression in server settings
   - [ ] Set browser caching headers
   - [ ] Consider CDN for `pictures/` folder

## Troubleshooting

**Problem**: Contact form doesn't send emails
**Solution**: Check PHP `mail()` function is enabled on your hosting. Some hosts require SMTP configuration.

**Problem**: 500 Internal Server Error
**Solution**: Check `.htaccess` files are uploaded correctly. Some hosts don't support certain directives.

**Problem**: Admin panel shows blank page
**Solution**: Check PHP version (requires PHP 7.4+). Check file permissions.

**Problem**: Images don't load
**Solution**: Verify folder structure is preserved. Check file names are case-sensitive.

## Next Steps

1. Point your domain to the hosting server
2. Install SSL certificate (Let's Encrypt is free)
3. Set up Google Analytics (optional)
4. Submit sitemap.xml to Google Search Console

---

**Support**: If you need help, contact your hosting provider's support team.
