# Design System Document: The LMS Arrahmah

## 1. Overview & Creative North Star

### The Creative North Star: "The Cognitive Sanctuary"

This design system rejects the cluttered, administrative look of traditional Learning Management Systems. Instead, it adopts the persona of a **Cognitive Sanctuary**—a space that feels as quiet as a high-end library but as inspiring as a modern gallery.

To break the "template" look, we move away from rigid, boxy grids. We utilize **intentional asymmetry**, where content is weighted to one side to create a natural eye-path, and **layered surfaces** that mimic physical sheets of translucent vellum. The goal is a "focused flow state" for students, achieved through generous negative space and a "High-End Editorial" layout that prioritizes readability over density.

---

## 2. Colors & Atmospheric Depth

Our palette is a sophisticated transition from deep, authoritative blues to energetic, intellectual teals, grounded in a pristine "Off-White" ecosystem.

### The "No-Line" Rule

**Sectioning must never be achieved with 1px solid lines.**
In this design system, boundaries are defined by tonal shifts. To separate a sidebar from a main content area, use `surface-container-low` against a `surface` background. The lack of hard lines reduces cognitive load and makes the UI feel "infinite" and premium.

### Surface Hierarchy & Nesting

Treat the UI as a series of physical layers. Use the `surface-container` tiers to create a "nested" depth:

- **Base:** `surface` (#f5f7f9) - The primary canvas.
- **Sectioning:** `surface-container-low` (#eef1f3) - For large secondary areas (e.g., sidebars).
- **Cards:** `surface-container-lowest` (#ffffff) - Reserved for interactive elements that need to "pop" forward.
- **Active Overlays:** `surface-container-high` (#dfe3e6) - For subtle emphasis on hover or selected states.

### The "Glass & Gradient" Rule

To inject "soul" into the professional aesthetic:

- **Hero Sections:** Use a subtle linear gradient from `primary` (#0058ba) to `primary-container` (#6c9fff) at a 135-degree angle.
- **Floating Navigation:** Apply a `surface-container-lowest` background with 80% opacity and a `20px backdrop-blur`. This creates a frosted-glass effect that keeps the student grounded in their current context.

---

## 3. Typography

We use a high-contrast pairing: **Manrope** for structural authority and **Inter** for sustained reading comfort.

- **Display & Headlines (Manrope):** These are your "Editorial Anchors." Large font sizes (`display-lg` at 3.5rem) should be used with tight letter-spacing (-0.02em) to create a bold, modern feel for course titles and module headers.
- **Body & Titles (Inter):** Inter’s tall x-height ensures legibility during long study sessions. Use `body-lg` (1rem) for primary lesson content to ensure the eye doesn't fatigue.
- **Labels:** Use `label-md` in `secondary` (#00675c) for meta-data like "Time to Complete" or "Points," providing a clear visual distinction from narrative text.

---

## 4. Elevation & Depth

Depth is achieved through **Tonal Layering**, not structural scaffolding.

- **The Layering Principle:** A white card (`surface-container-lowest`) placed on a light grey background (`surface-container-low`) creates a natural, soft lift. This is our primary method of hierarchy.
- **Ambient Shadows:** For floating elements (Modals/Popovers), use an extra-diffused shadow:
    - _Shadow:_ `0px 20px 40px rgba(44, 47, 49, 0.06)` (A tinted version of `on-surface`).
- **The "Ghost Border" Fallback:** If a border is required for accessibility, use `outline-variant` (#abadaf) at **15% opacity**. It should be felt, not seen.
- **Glassmorphism:** Use for persistent progress trackers. A glass-morphic pill floating at the bottom of the screen keeps the student motivated without blocking the content.

---

## 5. Components

### Buttons

- **Primary:** A gradient-filled container (`primary` to `primary-dim`) with `full` roundedness. No border. High-contrast `on-primary` text.
- **Secondary:** `surface-container-high` background with `primary` colored text. This feels integrated, not isolated.
- **Interaction:** On hover, primary buttons should scale slightly (1.02x) rather than just changing color.

### Progress Inputs (Checkboxes & Radios)

- **Roundedness:** Use the `sm` (0.5rem) token for checkboxes—avoid sharp corners.
- **State:** A "Completed" state uses `secondary` (#00675c) with a soft glow effect (`secondary-container`) to signal success and provide positive reinforcement.

### Cards & Lists

- **The Divider Forbiddance:** Never use `hr` tags. Separate list items using 16px of vertical whitespace or a subtle background shift to `surface-container-low` on hover.
- **Course Cards:** Use `xl` (3rem) rounded corners on the top-left only to create a signature, "designer" look that breaks the standard 4-side symmetry.

### The "Focus-Mode" Drawer

A unique component for this system. A slide-over panel using `surface-container-lowest` with a heavy backdrop blur (#f5f7f9 at 40%) that hides all navigation, leaving only the lesson text and a "Next" button.

---

## 6. Do's and Don'ts

### Do

- **Do** use the `xl` (3rem) roundedness for large layout containers to create a friendly, approachable atmosphere.
- **Do** utilize "Optical Centering"—place focal points slightly above the mathematical center to create a more balanced, editorial feel.
- **Do** use `tertiary-container` (#4dc9f1) for "Aha!" moments or hints; its vibrant teal provides an intellectual spark.

### Don't

- **Don't** use pure black (#000000) for text. Always use `on-surface` (#2c2f31) to maintain the "Soft Minimalism" vibe.
- **Don't** use more than one floating element per screen. It breaks the "Sanctuary" and creates anxiety.
- **Don't** use standard 12-column grids for everything. Experiment with wide margins (e.g., 15% left margin for headlines) to create an expensive, bespoke feel.
