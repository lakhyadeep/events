# Initial Client Requirements & Project Brief

## Project Overview
**Client/Brand**: Dib 24x7  
**Project**: Top masthead with sponsor integration event microsite & contest management system  
**Target Event (Flagship)**: Dib 24x7 Sharod Samman 2026 (Annual Puja & Festival Excellence Awards)  
**Host Environment**: Shared Server running PHP 8.3 & MySQL/MariaDB / SQLite  
**Stack**: Laravel 13, Filament 5 Admin Panel, Livewire 4, Tailwind CSS  

---

## 1. Raw Client Scope & Requirements Specification

### Frontend: Top Masthead with Sponsor Integration Event Microsite
* **Main Entity Logo**: Dib 24x7 official brand identity.
* **Event Logo**: Specific festival / contest edition logo.
* **Presenting Partner Logo**: Exclusive marquee title partner branding.
* **Associate Sponsors**: Maximum 5 slot provision with sponsor tags and URLs.
* **ATF Banner**: Above-The-Fold dynamic responsive hero carousel banner (Desktop 1920x600, Mobile 768x960).
* **About the Event**: Rich introduction, jury criteria, countdown clock, and closure states.
* **Participants Showcase**: Interactive card grid of contenders, searchable, filterable by zone/locality, with popup modal detail views.
* **Event Timeline**: Chronological milestones tracking nominations, jury inspections, voting periods, and gala finale.
* **Awards**: Master display of categories, prestige gold trophies, and cash prizes.
* **Timeline**: Status trackers (completed, ongoing, upcoming).
* **Terms and Conditions**: Official regulatory guidelines, fire & electrical compliance, voting disclaimer.

---

### Backend: Event / Contest Management
* **Fields**:
  * Event name
  * Event display name
  * Year
  * Page slug (URL friendly)
  * Registration status (toggle switch)
  * Voting status (toggle switch)
  * Awards status (toggle switch)
  * Timeline status (toggle switch)
  * About the event (Rich text editor)
  * Event tagline
  * Criteria (Rich text editor)
  * Closure message
  * Countdown date time
  * Terms and conditions (Rich text editor)
  * OG image (OpenGraph social share preview)
  * Page meta title
  * Page meta description
  * Contact email
  * Contact phone
  * External link
  * Event status (toggle switch)

---

### Frontend: Navbar - Main Menu
* **Navigation Links**:
  * About the awards
  * Selection Criteria
  * Timeline
  * Vote Now (Primary Call-to-Action)

---

### Backend: ATF Banner Management
* **Carousel banner engine**
* **Fields**:
  * Banner title
  * Image (Desktop & Mobile responsive uploads)
  * CTA link
  * Select Event / Contest
  * Year
  * Status (Active / Inactive toggle)

---

### Backend: Sponsors Management
* **Multi-tier sponsor hierarchy**
* **Fields**:
  * Sponsor title
  * Sponsor display name
  * Sponsor type (`presenting_partner`, `associate_sponsor`, `co_sponsor`, `beverage_partner`, etc.)
  * Sponsor tag (e.g. "Title Presenting Partner", "Associate Sponsor 1")
  * Sponsor logo
  * Sponsor description
  * Sponsor landing URL
  * Select event / contest
  * Year
  * Slot order (1 to 5)
  * Status (Active / Inactive toggle)

---

### Backend: Award Management
* **Fields**:
  * Award name
  * Award display name
  * Award description
  * Award category
  * Prize Money / Award (e.g., ₹2,50,000 + Gold Trophy)
  * Select Event / Contest
  * Year
  * Status (Active / Inactive toggle)

---

### Backend: Participant / Candidate Master
* **Core Details**:
  * Mobile Number
  * Event / Contest association
  * Participant Name
  * Participant Display Name
  * Short introduction
  * Zone (North, South, Central, East, West Kolkata / Suburban)
  * Locality
  * Address
  * Landmark
  * Key contact 1 name & phone
  * Key contact 2 name & phone
  * Primary display image
  * Gallery Images: Image 1, Image 2, Image 3
  * Year
  * Registration Status (`pending`, `approved`, `rejected`)
  * Is shortlisted (toggle)

* **Puja / Cultural Contest Specific Fields**:
  * First year of puja (Establishment year)
  * Puja Theme
  * Sound designer
  * Light designer
  * Idol Artist
  * Theme artist
  * Concept Note (image upload / document)

---

### Backend: Winner Master
* **Fields**:
  * Select Event / Contest
  * Select Award
  * Select Participant / Candidate
  * Year
  * Select order / rank (1st, 2nd, 3rd, Special Jury Mention)
  * Status (`draft`, `published`, `archived`)

---

### Backend: Event / Contest Video Shorts
* **Fields**:
  * Video name
  * Video platform (`youtube_shorts`, `youtube`, `vimeo`, `instagram_reel`, `direct_upload`)
  * Video URL
  * Video caption
  * Video thumbnail image
  * Year
  * Status (Active / Inactive toggle)

---

## 2. Architecture & Design Principles

1. **Headless API Support**: Fully isolated JSON REST endpoints (`/api/v1/events/{slug}`) for prospective mobile app or third-party syndication.
2. **Custom HTML / Design Flexibility**: Decoupled Blade components to allow custom frontend themes or complete layout restyling without altering the database schema or admin logic.
3. **Shared Hosting Compatibility**: Native artisan fallback routes (such as `/admin-tools/storage-link`) and zero reliance on daemon workers or Node servers in production.
