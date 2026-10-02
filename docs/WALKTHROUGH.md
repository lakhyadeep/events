# Dib 24x7 Event Microsite & Management System — Project Walkthrough

**Project Path:** `E:\Herd\events`  
**Local Herd Domain:** [http://events.test](http://events.test)  
**Admin Panel:** [http://events.test/admin](http://events.test/admin)  
**Admin Credentials:** `admin@dib24x7.com` / `password`  
**Test Suite Status:** 11 Feature Tests Passed (43 Assertions)

---

## 1. System Overview & Technology Stack

The project has been built in strict accordance with the client's specifications and technical requirements:

| Component | Technology | Version | Notes |
| :--- | :--- | :--- | :--- |
| **Backend Framework** | Laravel | `13.34.0` | PHP 8.3 compatible, configured for shared hosting |
| **Admin Panel** | Filament PHP | `5.9.0` | High-performance admin suite with schemas |
| **Reactive Frontend** | Livewire | `4.4.7` | Shared core with Filament 5 |
| **Styling & Interaction** | Tailwind CSS + Alpine.js | Modern | Embedded in Blade layout, responsive & modular |
| **API Architecture** | Headless REST API | v1 | Ready for standalone headless frontends |
| **Database** | SQLite (Dev) / MySQL (Prod) | Compatible | Auto-tested, `Schema::defaultStringLength(191)` configured |

---

## 2. Implemented Features & Modules

### Frontend Microsite
1. **Top Masthead with Sponsor Integration**:
   - Primary Entity: **Dib 24x7** branding.
   - Event branding: Title, year, tagline.
   - **Presenting Partner**: Dedicated high-visibility badge.
   - **Associate Sponsors**: Automatic constraint allowing a maximum of **5 slots** via Eloquent scope (`Sponsor::associate()`).
2. **Above-the-Fold (ATF) Banner Carousel**:
   - Multi-slide carousel supporting desktop & mobile responsive `picture` sources.
   - Direct CTA deep-linking.
3. **Event Timeline & Live Countdown**:
   - Alpine.js client-side countdown timer ticking to event closure.
   - Roadmap milestone tracker with status markers (Upcoming, In Progress, Completed).
4. **Participants & Puja Cultural Showcase**:
   - Real-time search by candidate/club name or locality.
   - Zone pill filter tabs (e.g. South Kolkata, North Kolkata, Central, Salt Lake).
   - "Shortlisted Only" toggle filter.
   - Cultural/Puja specific attributes: First year of Puja, Puja Theme, Sound Designer, Light Designer, Idol Artist, Theme Artist, and Concept Note.
   - Interactive candidate detail modal with 4-image gallery and direct voting/shortlist action.
5. **Video Shorts Module**:
   - 9:16 vertical shorts layout with embedded playback modal (YouTube Shorts, YouTube, Vimeo).
6. **Awards & Winners Podium**:
   - Categorized award cards with prize money/trophy badges.
   - Hall of Fame / Winner podium celebrating 1st, 2nd, and 3rd ranks.
7. **Terms & Conditions**:
   - Expandable accordion for official rules, eligibility, and legal guidelines.

---

### Backend Admin Panel (Filament 5 at `/admin`)

All 8 requested resource entities are managed via dedicated Filament Resources:
- **Events / Contests**: Configure slug, year, active flags, toggles (`registration_status`, `voting_status`, `awards_status`, `timeline_status`), countdown datetime, meta tags, and closure message.
- **ATF Banners**: Manage banner title, desktop and mobile images, CTA URL, sort order, and event binding.
- **Sponsors**: Categorized by `presenting`, `associate`, `powered_by`, `co_sponsor`, `beverage_partner`, etc., with logo uploads and outbound landing links.
- **Awards**: Award name, category, prize money/trophy, and display order.
- **Participants / Candidates**: Master candidate directory with full puja fields and 4-slot image upload gallery.
- **Winners**: Link Award + Participant with Rank/Order (`1st`, `2nd`, `3rd`, etc.) and publication status.
- **Video Shorts**: Platform selector, video URL/ID, and 9:16 thumbnail image.
- **Timeline Milestones**: Event schedule checkpoints with execution statuses.

---

## 3. Decoupled Frontend Architecture: Future HTML/CSS Customization

To honor the requirement that **"the public-facing HTML may be custom designed in the future"**, the frontend has been completely decoupled using two layers:

### Option A: Modular Blade Overrides (Zero Backend Changes)
All public-facing sections reside in modular Blade component files under `resources/views/components/`:
- `resources/views/components/masthead.blade.php`: Masthead layout & 5 sponsor slots.
- `resources/views/components/navbar.blade.php`: Navigation bar & Vote CTA.
- `resources/views/components/banner-carousel.blade.php`: ATF banner slider.
- `resources/views/components/countdown.blade.php`: Countdown widget & event intro.
- `resources/views/components/awards-winners.blade.php`: Awards matrix and podium.
- `resources/views/components/video-shorts.blade.php`: 9:16 vertical shorts player.
- `resources/views/components/timeline.blade.php`: Milestone roadmap.
- `resources/views/components/terms-footer.blade.php`: Terms accordion & footer.

Any frontend developer or UI agency can replace the markup or Tailwind classes in these individual files without touching any controller, model, or database logic.

### Option B: Headless REST API (Headless Frontends)
If an agency supplies a decoupled Next.js, Nuxt, Astro, or static HTML client, the complete API is already live:
- `GET /api/v1/events/{slug}`: Complete event payload including masthead sponsor hierarchy, toggles, countdown, and banners.
- `GET /api/v1/events/{slug}/participants?search=...&zone=...&shortlisted=1`: Paginated candidate data.
- `GET /api/v1/events/{slug}/shorts`: Active video shorts.
- `GET /api/v1/events/{slug}/winners`: Published winners podium.

---

## 4. Shared Server (cPanel / PHP 8.3) Deployment Guide

When deploying this project to your shared hosting server:
1. **PHP Version**: Ensure PHP 8.3 is selected in cPanel MultiPHP Manager.
2. **Document Root**: Point the domain document root to the `public/` directory (e.g. `public_html/events/public` or symlink `public_html` to `events/public`).
3. **Database Setup**:
   - Create a MySQL database and user in cPanel.
   - Update `.env`:
     ```ini
     DB_CONNECTION=mysql
     DB_HOST=127.0.0.1
     DB_PORT=3306
     DB_DATABASE=your_cpanel_dbname
     DB_USERNAME=your_cpanel_dbuser
     DB_PASSWORD=your_cpanel_password
     ```
   - Run `php artisan migrate --seed` (or import via phpMyAdmin).
4. **Storage Symlink on Shared Hosting (No SSH Required)**:
   - Simply navigate in your browser to:
     ```
     https://yourdomain.com/admin-tools/storage-link
     ```
   - This trigger executes `Artisan::call('storage:link')` safely through the browser and returns `Storage link created successfully.`

---

## 5. Automated Test Verification

All tests can be executed locally inside `E:\Herd\events` at any time:
```powershell
php artisan test
```
**Test Results:**
```
PASS  Tests\Feature\AdminManagementTest
✓ admin user can access filament login
✓ admin user can authenticate and access dashboard
✓ associate sponsors scope limits to five
✓ winner model relationships

PASS  Tests\Feature\EventMicrositeTest
✓ flagship event microsite renders successfully
✓ slug based event route works
✓ headless api event endpoint returns json
✓ headless api participants filter by zone
✓ livewire participants showcase component filters candidates

Tests:    11 passed (43 assertions)
Duration: 1.29s
```
