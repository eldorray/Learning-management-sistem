// Entries copied from the registered LandingPages.tsx (Kage: lines 65–69, Meng To Sketchbook: lines 620–624).
// The complete, checksum-verified source bundle is retained in resources/threeui.
import { splitTypographyProps, usePageTypography, type PageTypographyProps } from '../threeui/src/shaders/landing-pages/pageTypography';
import { LandingPageFrame, type LandingPageProps } from '../threeui/src/shaders/landing-pages/LandingPageFrame';
import { KAGE_TYPOGRAPHY, MENG_TO_SKETCHBOOK_TYPOGRAPHY } from '../threeui/src/shaders/landing-pages/pageRecipes';

export function KageLandingPage(props: LandingPageProps & PageTypographyProps) {
  const [type, frame] = splitTypographyProps(props);
  const customization = usePageTypography(KAGE_TYPOGRAPHY, type);
  return <LandingPageFrame {...frame} customization={customization} title="Kage — Where stillness reveals the unseen" sourceUrl="/landing-pages/kage.html" />;
}

// Only the iframe title is localised; the frame, recipe and source URL are unchanged.
export function MengToSketchbookLandingPage(props: LandingPageProps & PageTypographyProps) {
  const [type, frame] = splitTypographyProps(props);
  const customization = usePageTypography(MENG_TO_SKETCHBOOK_TYPOGRAPHY, type);
  return <LandingPageFrame {...frame} customization={customization} title="LMS Ar-Rahmah — Buku Sketsa" sourceUrl="/landing-pages/meng-to-sketchbook.html" />;
}
