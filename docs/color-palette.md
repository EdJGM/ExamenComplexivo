# Examen Complexivo — Color Palette Guide

## Brand Theme Overview
- Primary brand tone is a green palette mapped to Bootstrap tokens.
- Accents use darker/neutral tones for contrast and accessibility.
- Info color is customized (teal/blue) for better readability.

## Core Brand Colors
- Primary: #0EA44B (emerald green)
- Primary Dark: #0A7D39
- Primary Light: #22C55E
- Success: #22C55E
- Warning: #F59E0B
- Danger: #DC2626
- Info: #0EA5E9 (custom override)
- Secondary: #6C757D
- Light: #F8F9FA
- Dark: #232830

## Bootstrap Token Mapping
Use these values to configure Bootstrap (via SCSS variables or CSS custom properties):
- `--bs-primary`: #0EA44B
- `--bs-primary-rgb`: 14, 164, 75
- `--bs-success`: #22C55E
- `--bs-warning`: #F59E0B
- `--bs-danger`: #DC2626
- `--bs-info`: #0EA5E9
- `--bs-secondary`: #6C757D
- `--bs-light`: #F8F9FA
- `--bs-dark`: #232830

## UI Component Tokens
Buttons and badges use the following derived tones for states:
- Primary Button
  - Base: #0EA44B
  - Hover: #0A7D39
  - Active: #085F2D
  - Focus Shadow RGB: 14, 164, 75
- Info Button (customized)
  - Base: #0EA5E9
  - Hover: #0284C7
  - Active: #0369A1
  - Focus Shadow RGB: 14, 165, 233

Alerts and subtle backgrounds:
- Info Alert
  - Bg Subtle: rgba(14, 165, 233, 0.125)
  - Border Subtle: rgba(14, 165, 233, 0.375)
  - Text Emphasis: #0369A1
- Primary Subtle
  - Bg Subtle: rgba(14, 164, 75, 0.12)
  - Border Subtle: rgba(14, 164, 75, 0.35)
  - Text Emphasis: #085F2D

## Semantic Usage Guidelines
- Primary: Actions, highlights, navigation accents, selection states.
- Success: Completed steps, positive statuses, confirmations.
- Warning: Cautionary actions, pending confirmations.
- Danger: Destructive actions, errors, failed validations.
- Info: Informational notices, contextual hints, non-critical banners.
- Secondary/Neutral: Borders, placeholders, secondary text.

## Accessibility Notes
- Target contrast ratio ≥ 4.5:1 for body text.
- Prefer dark-on-light text for readability; use `--bs-dark` on `--bs-light` backgrounds.
- For buttons, ensure hover/active states increase contrast and are keyboard-focus visible.

## Background Imagery Overlays
When placing text over images (navbar/sidebar), use dark overlays:
- Navbar Overlay: `linear-gradient(rgba(0,0,0,.70), rgba(0,0,0,.70))`
- Sidebar Overlay: `linear-gradient(rgba(0,0,0,.65), rgba(0,0,0,.65))`

## Implementation Snippets
SCSS variable overrides:
```scss
// resources/sass/_variables.scss
$primary: #0EA44B;
$success: #22C55E;
$warning: #F59E0B;
$danger: #DC2626;
$info: #0EA5E9;
$secondary: #6C757D;
$light: #F8F9FA;
$dark: #232830;
```

CSS custom properties (optional runtime overrides):
```css
:root {
  --bs-primary: #0EA44B;
  --bs-success: #22C55E;
  --bs-warning: #F59E0B;
  --bs-danger: #DC2626;
  --bs-info: #0EA5E9;
  --bs-secondary: #6C757D;
  --bs-light: #F8F9FA;
  --bs-dark: #232830;
}
```

Component state tokens for buttons:
```css
.btn-primary { background-color: #0EA44B; border-color: #0EA44B; }
.btn-primary:hover { background-color: #0A7D39; border-color: #0A7D39; }
.btn-primary:active { background-color: #085F2D; border-color: #085F2D; }

.btn-info { background-color: #0EA5E9; border-color: #0EA5E9; }
.btn-info:hover { background-color: #0284C7; border-color: #0284C7; }
.btn-info:active { background-color: #0369A1; border-color: #0369A1; }
```

## Usage Across Systems
- Keep the token names identical to ensure drop-in compatibility.
- Prefer SCSS variable overrides when building with Bootstrap; use CSS variables for runtime theming.
- Share this file with teams to align on color decisions and ensure consistent UX.
