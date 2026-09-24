// Kage-only entry from the registered LandingPages.tsx (lines 65–69).
// The complete, checksum-verified source bundle is retained in resources/threeui.
import { splitTypographyProps, usePageTypography, type PageTypographyProps } from '../threeui/src/shaders/landing-pages/pageTypography';
import { LandingPageFrame, type LandingPageProps } from '../threeui/src/shaders/landing-pages/LandingPageFrame';
import { KAGE_TYPOGRAPHY } from '../threeui/src/shaders/landing-pages/pageRecipes';

export function KageLandingPage(props: LandingPageProps & PageTypographyProps) {
  const [type, frame] = splitTypographyProps(props);
  const customization = usePageTypography(KAGE_TYPOGRAPHY, type);
  return <LandingPageFrame {...frame} customization={customization} title="Kage — Where stillness reveals the unseen" sourceUrl="/landing-pages/kage.html" />;
}
