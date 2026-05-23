# Black Sky Enterprise - Design System

> **Source of Truth untuk UI/UX Design Consistency**  
> Dokumen ini adalah panduan resmi untuk memastikan konsistensi visual dan pengalaman pengguna di seluruh platform Black Sky Enterprise.

---

## 📋 Table of Contents

1. [Brand Identity](#brand-identity)
2. [Color System](#color-system)
3. [Typography](#typography)
4. [Spacing & Layout](#spacing--layout)
5. [Components](#components)
6. [Motion & Animation](#motion--animation)
7. [Visual Effects](#visual-effects)
8. [Responsive Design](#responsive-design)
9. [Accessibility](#accessibility)

---

## 🎨 Brand Identity

### Brand Essence
Black Sky Enterprise adalah premier concert promoter yang menghadirkan pengalaman live entertainment yang unforgettable. Design system mencerminkan:
- **Premium & Professional**: Kualitas tinggi dalam setiap detail
- **Energetic & Dynamic**: Mencerminkan energi live concert
- **Modern & Bold**: Typography yang kuat dan layout yang berani
- **Immersive**: Penggunaan visual yang engaging dan interactive

### Brand Colors
- **Primary**: Purple/Violet (`#A855F7`) - Energi dan kreativitas
- **Secondary**: Cyan/Blue (`#0EA5E9`, `#0284C7`) - Trust dan profesionalisme
- **Accent**: Rose/Red (`#E11D48`) - Passion dan excitement
- **Base**: Deep Black (`#050505`, `#030213`) - Premium dan sophisticated

---

## 🎨 Color System

### Light Mode (Default)

#### Primary Colors
```css
--background: #ffffff
--foreground: oklch(0.145 0 0)  /* Near black */
--primary: #030213              /* Deep navy black */
--primary-foreground: oklch(1 0 0)  /* White */
```

#### Secondary & Accent
```css
--secondary: oklch(0.95 0.0058 264.53)  /* Light purple-gray */
--secondary-foreground: #030213
--accent: #e9ebef                       /* Light gray-blue */
--accent-foreground: #030213
```

#### Semantic Colors
```css
--muted: #ececf0                /* Subtle gray */
--muted-foreground: #717182     /* Medium gray text */
--destructive: #d4183d          /* Error red */
--destructive-foreground: #ffffff
--border: rgba(0, 0, 0, 0.1)    /* Subtle borders */
```

#### Input & Interactive
```css
--input: transparent
--input-background: #f3f3f5
--switch-background: #cbced4
--ring: oklch(0.708 0 0)        /* Focus ring */
```

### Dark Mode

#### Primary Colors
```css
--background: oklch(0.145 0 0)  /* Deep black */
--foreground: oklch(0.985 0 0)  /* Near white */
--primary: oklch(0.985 0 0)     /* White */
--primary-foreground: oklch(0.205 0 0)  /* Dark gray */
```

#### Secondary & Accent
```css
--secondary: oklch(0.269 0 0)   /* Dark gray */
--secondary-foreground: oklch(0.985 0 0)
--accent: oklch(0.269 0 0)
--accent-foreground: oklch(0.985 0 0)
--muted: oklch(0.269 0 0)
--muted-foreground: oklch(0.708 0 0)
```

### Brand Gradient Colors

#### Purple Gradient (Primary)
```css
/* Gradient untuk hero text dan highlights */
linear-gradient(to right, #5227FF, #FF9FFC, #B497CF)
```

#### Cyan/Blue Gradient (Secondary)
```css
/* Untuk buttons dan interactive elements */
linear-gradient(90deg, rgba(14,165,233,0.16), rgba(14,165,233,0.98))
```

#### Glow Effects
```css
/* Purple glow */
rgba(168,85,247,0.25)  /* Soft purple */
rgba(168,85,247,0.35)  /* Selection highlight */

/* Cyan glow */
rgba(14,165,233,0.2)   /* Soft cyan */
rgba(14,165,233,0.75)  /* Strong cyan glow */

/* Rose glow */
rgba(225,29,72,0.12)   /* Soft rose */
```

### Chart Colors
```css
--chart-1: oklch(0.646 0.222 41.116)   /* Orange */
--chart-2: oklch(0.6 0.118 184.704)    /* Teal */
--chart-3: oklch(0.398 0.07 227.392)   /* Blue */
--chart-4: oklch(0.828 0.189 84.429)   /* Yellow */
--chart-5: oklch(0.769 0.188 70.08)    /* Lime */
```

---

## ✍️ Typography

### Font Families

#### Primary: Barlow Condensed
**Usage**: Headlines, titles, buttons, labels
```css
font-family: 'Barlow Condensed', sans-serif;
```
**Weights Available**: 400, 600, 700, 800, 900
**Characteristics**: 
- Condensed untuk impact maksimal
- Uppercase untuk emphasis
- Wide letter-spacing untuk readability

#### Secondary: Barlow
**Usage**: Body text, descriptions
```css
font-family: 'Barlow', sans-serif;
```
**Weights Available**: 300, 400, 500, 600

#### Tertiary: Inter
**Usage**: UI elements, forms, general text
```css
font-family: 'Inter', sans-serif;
```
**Weights Available**: 300, 400, 500, 600

### Type Scale

#### Headings
```css
/* H1 - Hero Headlines */
font-size: clamp(3.5rem, 17vw, 13rem);
font-family: 'Barlow Condensed', sans-serif;
font-weight: 900;
line-height: 0.76;
letter-spacing: -0.025em;
text-transform: uppercase;

/* H2 - Section Titles */
font-size: var(--text-xl);
font-weight: 500;  /* medium */
line-height: 1.5;

/* H3 - Subsection Titles */
font-size: var(--text-lg);
font-weight: 500;
line-height: 1.5;

/* H4 - Card Titles */
font-size: var(--text-base);
font-weight: 500;
line-height: 1.5;
```

#### Body Text
```css
/* Large Body */
font-size: clamp(0.9rem, 1.5vw, 1.15rem);
font-family: 'Barlow', sans-serif;
font-weight: 300;
line-height: 1.6;

/* Regular Body */
font-size: var(--text-base);
font-weight: 400;
line-height: 1.5;
```

#### Labels & Buttons
```css
/* Primary Button / CTA */
font-family: 'Barlow Condensed', sans-serif;
font-weight: 700;
font-size: clamp(10.5px, 2vw, 13px);
letter-spacing: 0.18em;
text-transform: uppercase;

/* Small Labels */
font-family: 'Barlow Condensed', sans-serif;
font-weight: 600;
font-size: clamp(8px, 1.6vw, 12px);
letter-spacing: clamp(0.18em, 0.45vw, 0.34em);
text-transform: uppercase;

/* Ticker / Marquee Text */
font-family: 'Barlow Condensed', sans-serif;
font-weight: 800;
font-size: 0.72em;
letter-spacing: 0.18em;
text-transform: uppercase;
```

### Typography Guidelines

1. **Uppercase Usage**: Gunakan uppercase untuk:
   - Headlines utama
   - Buttons dan CTAs
   - Labels dan tags
   - Navigation items

2. **Letter Spacing**: 
   - Tight (-0.025em) untuk headlines besar
   - Wide (0.18em - 0.34em) untuk uppercase text kecil
   - Normal untuk body text

3. **Line Height**:
   - Tight (0.76 - 1.0) untuk display headlines
   - Comfortable (1.5 - 1.6) untuk body text

---

## 📐 Spacing & Layout

### Spacing Scale
```css
--radius: 0.625rem;  /* 10px - Base radius */
--radius-sm: calc(var(--radius) - 4px);   /* 6px */
--radius-md: calc(var(--radius) - 2px);   /* 8px */
--radius-lg: var(--radius);               /* 10px */
--radius-xl: calc(var(--radius) + 4px);   /* 14px */
```

### Container & Max Width
```css
/* Main content container */
max-width: 1600px;
margin: 0 auto;
padding: 0 clamp(2rem, 4vw, 4rem);
```

### Section Spacing
```css
/* Vertical section padding */
padding-top: clamp(4rem, 8vw, 8rem);
padding-bottom: clamp(4rem, 8vw, 8rem);

/* Hero section */
min-height: 100vh;
padding-top: 6rem;  /* Account for navbar */
padding-bottom: 6rem;
```

### Grid & Gap
```css
/* Component gaps */
--gap-xs: 0.5rem;   /* 8px */
--gap-sm: 1rem;     /* 16px */
--gap-md: 1.5rem;   /* 24px */
--gap-lg: 2rem;     /* 32px */
--gap-xl: 3rem;     /* 48px */

/* Dot grid spacing */
--dot-size: 16px;
--dot-gap: 32px;

/* Logo loop spacing */
--logoloop-gap: 32px;
--logoloop-logoHeight: 26px;
```

---

## 🧩 Components

### Buttons

#### Primary Button (CTA)
```css
/* Solid button with clip-path */
background: #0284C7;
font-family: 'Barlow Condensed', sans-serif;
font-weight: 700;
font-size: clamp(10.5px, 2vw, 13px);
letter-spacing: 0.18em;
color: #fff;
padding: 0.75rem 2rem;
clip-path: polygon(10px 0%, 100% 0%, calc(100% - 10px) 100%, 0% 100%);
transition: all 0.3s;

/* Hover state */
opacity: 0.9;
gap: 1.25rem;  /* Increase gap on hover */
```

#### Secondary Button (Ticket Style)
```css
/* Outer border with animated gradient */
.ticket-border {
  background: linear-gradient(
    90deg,
    rgba(14, 165, 233, 0.16),
    rgba(14, 165, 233, 0.98),
    rgba(14, 165, 233, 0.38),
    rgba(14, 165, 233, 0.98),
    rgba(14, 165, 233, 0.16)
  );
  background-size: 320% 100%;
  animation: ticketBorderFlow 5s steps(125) infinite;
  clip-path: polygon(10px 0%, 100% 0%, calc(100% - 10px) 100%, 0% 100%);
  padding: 1px;
}

/* Inner button */
background: rgba(3,7,12,0.72);
color: rgba(255,255,255,0.82);
padding: 0.75rem 2rem;

/* Hover state */
background: rgba(14,165,233,0.18);
color: #fff;
```

#### Ghost Button (Navigation)
```css
background: none;
border: none;
color: rgba(255,255,255,0.65);
font-family: 'Barlow Condensed', sans-serif;
font-weight: 600;
font-size: 13px;
letter-spacing: 0.2em;
position: relative;

/* Hover underline */
&::after {
  content: '';
  position: absolute;
  bottom: -4px;
  left: 0;
  width: 0;
  height: 1px;
  background: #A855F7;
  transition: width 0.3s;
}

&:hover::after {
  width: 100%;
}
```

### Cards

#### Base Card
```css
background: rgba(255,255,255,0.05);
border: 1px solid rgba(255,255,255,0.1);
border-radius: 0.75rem;
padding: 1.5rem;
backdrop-filter: blur(10px);
```

#### Hover Effect
```css
transition: all 0.3s ease;

&:hover {
  transform: translateY(-4px);
  border-color: rgba(168,85,247,0.3);
  box-shadow: 0 8px 32px rgba(168,85,247,0.15);
}
```

### Navigation Bar

#### Desktop Navbar
```css
/* Scrolled state */
background: linear-gradient(135deg, rgba(7,18,33,0.62), rgba(4,8,16,0.46));
backdrop-filter: blur(24px) saturate(160%);
border-bottom: 1px solid rgba(255,255,255,0.14);
box-shadow: 0 18px 50px rgba(0,0,0,0.34), inset 0 1px 0 rgba(255,255,255,0.08);

/* Initial state */
background: linear-gradient(to bottom, rgba(5,5,5,0.7) 0%, transparent 100%);
```

#### Mobile Menu
```css
/* Full screen overlay */
position: fixed;
inset: 0;
background: #050505;
z-index: 100;

/* Menu items */
font-family: 'Barlow Condensed', sans-serif;
font-weight: 800;
font-size: clamp(2.5rem, 8vw, 4rem);
letter-spacing: 0.05em;
border-bottom: 1px solid rgba(255,255,255,0.08);
```

### Gradient Text

#### Animated Gradient
```css
background-image: linear-gradient(to right, #5227FF, #FF9FFC, #B497CF, #5227FF);
background-size: 300% 100%;
background-clip: text;
-webkit-background-clip: text;
-webkit-text-fill-color: transparent;
animation: gradientFlow 8s ease-in-out infinite;
```

### Logo Loop / Ticker

#### Container
```css
border-top: 1px solid rgba(255,255,255,0.06);
background: rgba(5,5,5,0.8);
backdrop-filter: blur(10px);
overflow: hidden;
```

#### Animation
```css
/* Smooth continuous scroll */
animation: ticker 30s linear infinite;
will-change: transform;

/* Hover pause (optional) */
&:hover {
  animation-play-state: paused;
}
```

### Dot Grid (Interactive Background)

#### Configuration
```css
--dot-size: 16px;
--dot-gap: 32px;
--base-color: #5227FF;
--active-color: #5227FF;
--proximity: 150px;
--speed-trigger: 100px;
--shock-radius: 250px;
```

#### Behavior
- Dots respond to mouse proximity with color change
- Fast mouse movement triggers inertia physics
- Click creates shockwave effect
- Elastic return animation

---

## 🎬 Motion & Animation

### Animation Principles

1. **Smooth & Natural**: Gunakan easing curves yang natural
2. **Purposeful**: Setiap animasi harus memiliki tujuan
3. **Performance**: Gunakan `transform` dan `opacity` untuk performa optimal
4. **Responsive**: Animasi harus adapt dengan device capabilities

### Easing Functions

```css
/* Standard easing */
ease: [0.22, 1, 0.36, 1]  /* Smooth ease-out */

/* Elastic (untuk playful effects) */
ease: "elastic.out(1, 0.75)"

/* Steps (untuk retro/digital effects) */
animation: effect 5s steps(125) infinite;
```

### Common Animations

#### Fade In Up
```css
initial: { opacity: 0, y: 60 }
animate: { opacity: 1, y: 0 }
transition: { delay: 0.7, duration: 1, ease: [0.22, 1, 0.36, 1] }
```

#### Parallax Scroll
```css
const bgY = useTransform(scrollY, [0, 800], [0, 200]);
const opacity = useTransform(scrollY, [0, 500], [1, 0]);
```

#### Hover Scale
```css
transition: transform 0.3s ease;

&:hover {
  transform: scale(1.05);
}
```

#### Continuous Scroll (Ticker)
```css
@keyframes ticker {
  from { transform: translateX(0); }
  to { transform: translateX(-33.333%); }
}

animation: ticker 30s linear infinite;
```

#### Pulse Effect
```css
@keyframes pulse {
  from { opacity: 0.55; transform: scaleY(0.78); }
  to { opacity: 1; transform: scaleY(1); }
}

animation: pulse 3s steps(75) infinite alternate;
```

#### Scroll Indicator
```css
animate: { y: [0, 8, 0] }
transition: { duration: 1.5, repeat: Infinity, ease: "easeInOut" }
```

### Animation Timing

```css
/* Stagger delays for list items */
delay: index * 0.08  /* 80ms between items */

/* Section entrance */
delay: 0.5 - 1.0s

/* Micro-interactions */
duration: 0.3s

/* Page transitions */
duration: 0.8 - 1.0s
```

---

## ✨ Visual Effects

### Glassmorphism

```css
background: rgba(255, 255, 255, 0.05);
backdrop-filter: blur(24px) saturate(160%);
-webkit-backdrop-filter: blur(24px) saturate(160%);
border: 1px solid rgba(255, 255, 255, 0.14);
```

### Glow Effects

#### Purple Glow
```css
box-shadow: 0 0 8px rgba(168,85,247,0.75), 
            0 0 18px rgba(168,85,247,0.35);
text-shadow: 0 0 8px rgba(168,85,247,0.75), 
             0 0 18px rgba(168,85,247,0.35);
```

#### Cyan Glow
```css
box-shadow: 0 0 12px rgba(14,165,233,0.65),
            0 0 24px rgba(14,165,233,0.3);
```

### Gradient Overlays

#### Vignette Effect
```css
/* Dark edges */
background: linear-gradient(
  180deg, 
  rgba(5,5,5,0.35) 0%, 
  rgba(5,5,5,0.2) 40%, 
  rgba(5,5,5,0.85) 80%, 
  rgba(5,5,5,1) 100%
);

/* Side vignette */
background: linear-gradient(
  90deg, 
  rgba(5,5,5,0.8) 0%, 
  transparent 50%
);
```

#### Fade Out Edges
```css
/* Left fade */
background: linear-gradient(
  90deg, 
  rgba(3,7,12,0.95) 0%, 
  rgba(3,7,12,0.55) 45%, 
  transparent 100%
);
```

### Glow Orbs (Ambient Light)

```css
/* Purple orb */
width: 600px;
height: 600px;
background: radial-gradient(
  circle, 
  rgba(168,85,247,0.25) 0%, 
  transparent 70%
);
filter: blur(40px);
pointer-events: none;

/* Cyan orb */
background: radial-gradient(
  circle, 
  rgba(14,165,233,0.2) 0%, 
  transparent 70%
);

/* Rose orb */
background: radial-gradient(
  circle, 
  rgba(225,29,72,0.12) 0%, 
  transparent 70%
);
```

### Clip Path (Ticket Shape)

```css
/* Angled corners */
clip-path: polygon(
  10px 0%, 
  100% 0%, 
  calc(100% - 10px) 100%, 
  0% 100%
);
```

### Custom Scrollbar

```css
/* Firefox */
* {
  scrollbar-width: thin;
  scrollbar-color: rgba(168,85,247,0.3) transparent;
}

/* Webkit (Chrome, Safari) */
*::-webkit-scrollbar {
  width: 4px;
}

*::-webkit-scrollbar-track {
  background: transparent;
}

*::-webkit-scrollbar-thumb {
  background: rgba(168,85,247,0.3);
  border-radius: 2px;
}
```

### Text Selection

```css
::selection {
  background: rgba(168,85,247,0.35);
  color: #fff;
}
```

---

## 📱 Responsive Design

### Breakpoints

```css
/* Mobile First Approach */
/* xs: 0px - 639px (default) */
/* sm: 640px */
/* md: 768px */
/* lg: 1024px */
/* xl: 1280px */
/* 2xl: 1536px */
```

### Responsive Typography

```css
/* Use clamp() for fluid typography */
font-size: clamp(min, preferred, max);

/* Examples */
/* Hero headline */
font-size: clamp(3.5rem, 17vw, 13rem);

/* Body text */
font-size: clamp(0.9rem, 1.5vw, 1.15rem);

/* Button text */
font-size: clamp(10.5px, 2vw, 13px);

/* Small labels */
font-size: clamp(8px, 1.6vw, 12px);
```

### Responsive Spacing

```css
/* Padding */
padding: clamp(2rem, 4vw, 4rem);

/* Section spacing */
padding-top: clamp(4rem, 8vw, 8rem);
padding-bottom: clamp(4rem, 8vw, 8rem);

/* Gap */
gap: clamp(1rem, 2vw, 2rem);
```

### Mobile Adaptations

#### Navigation
- Desktop: Horizontal menu dengan inline links
- Mobile: Hamburger menu dengan full-screen overlay

#### Hero Section
```css
/* Desktop */
justify-content: end;  /* Content at bottom */
padding-bottom: 8rem;

/* Mobile */
justify-content: center;  /* Content centered */
padding-top: 6rem;
padding-bottom: 6rem;
```

#### Buttons
```css
/* Desktop */
flex-direction: row;
gap: 1rem;

/* Mobile */
flex-direction: column;
gap: 0.75rem;
width: 100%;
```

---

## ♿ Accessibility

### Focus States

```css
/* Visible focus ring */
outline: none;
focus-visible:ring-ring/50;
focus-visible:ring-[3px];
focus-visible:border-ring;
```

### ARIA Labels

```tsx
/* Navigation */
<nav aria-label="Main navigation">

/* Buttons */
<button aria-label="Open mobile menu">

/* Regions */
<section role="region" aria-label="Partner logos">

/* Lists */
<ul role="list">
  <li role="listitem">
```

### Semantic HTML

```tsx
/* Use proper heading hierarchy */
<h1> - Page title (once per page)
<h2> - Section titles
<h3> - Subsection titles
<h4> - Card/component titles

/* Use semantic elements */
<nav>, <main>, <section>, <article>, <footer>
```

### Color Contrast

- Minimum contrast ratio: **4.5:1** untuk normal text
- Minimum contrast ratio: **3:1** untuk large text (18px+)
- Gunakan tools seperti WebAIM Contrast Checker

### Keyboard Navigation

```tsx
/* Smooth scroll untuk keyboard users */
html {
  scroll-behavior: smooth;
}

/* Skip to main content */
<a href="#main-content" className="sr-only focus:not-sr-only">
  Skip to main content
</a>
```

### Screen Reader Support

```tsx
/* Hide decorative elements */
<div aria-hidden="true">

/* Provide alternative text */
<img alt="Descriptive text" />

/* Use sr-only for visual-only content */
<span className="sr-only">Additional context</span>
```

---

## 🎯 Best Practices

### Performance

1. **Optimize Images**
   - Use WebP format dengan fallback
   - Implement lazy loading: `loading="lazy"`
   - Provide proper width/height attributes

2. **Animations**
   - Use `transform` dan `opacity` (GPU-accelerated)
   - Add `will-change` untuk complex animations
   - Respect `prefers-reduced-motion`

3. **Code Splitting**
   - Lazy load components yang tidak immediately visible
   - Split vendor bundles

### Consistency

1. **Component Reusability**
   - Gunakan component library (shadcn/ui)
   - Create custom components untuk patterns yang berulang
   - Document component props dan usage

2. **Naming Conventions**
   - BEM untuk CSS classes: `block__element--modifier`
   - Descriptive component names: `HeroSection`, `LogoLoop`
   - Consistent file naming: PascalCase untuk components

3. **Code Organization**
   ```
   src/
   ├── app/
   │   ├── components/
   │   │   ├── ui/          # Reusable UI components
   │   │   ├── sections/    # Page sections
   ├── assets/              # Images, fonts, etc.
   ├── styles/              # Global styles
   │   ├── fonts.css
   │   ├── theme.css
   │   └── tailwind.css
   └── utils/               # Helper functions
   ```

### Testing

1. **Visual Regression Testing**
   - Test di multiple browsers
   - Test di different screen sizes
   - Test dark/light mode

2. **Accessibility Testing**
   - Keyboard navigation
   - Screen reader compatibility
   - Color contrast validation

3. **Performance Testing**
   - Lighthouse scores
   - Core Web Vitals
   - Animation performance

---

## 📚 Resources

### Design Tools
- **ColorBox**: Color palette generation
- **Type Scale**: Typography scale calculator

### Development Tools
- **Tailwind CSS**: Utility-first CSS framework
- **Framer Motion**: Animation library
- **GSAP**: Advanced animations
- **shadcn/ui**: Component library

### Inspiration
- **Awwwards**: Web design inspiration
- **Dribbble**: UI/UX design patterns
- **CodePen**: Interactive demos

---

## 🔄 Version History

- **v1.0.0** (2024) - Initial design system documentation
  - Established color palette
  - Defined typography scale
  - Documented component patterns
  - Created animation guidelines

---

## 📝 Notes

### Future Considerations

1. **Dark Mode Enhancement**
   - Refine dark mode color palette
   - Add theme toggle component
   - Test readability in both modes

2. **Component Library Expansion**
   - Add more reusable components
   - Create Storybook documentation
   - Build design tokens system

3. **Internationalization**
   - Support for multiple languages
   - RTL layout support
   - Locale-specific formatting

4. **Advanced Interactions**
   - 3D effects dengan Three.js
   - WebGL backgrounds
   - Advanced scroll animations

---

**Maintained by**: Black Sky Enterprise Design Team  
**Last Updated**: 2024  
**Contact**: design@blackskyenterprise.com
