import { createRoot } from "react-dom/client";
import ShapeGrid from "./app/components/ShapeGrid";

const mountShapeGrid = () => {
  const layout = document.querySelector(".fi-simple-layout");
  if (!layout || document.querySelector(".bsa-auth-shape-grid-host")) return;

  const host = document.createElement("div");
  host.className = "bsa-auth-shape-grid-host";
  host.setAttribute("aria-hidden", "true");
  layout.prepend(host);

  createRoot(host).render(
    <ShapeGrid
      direction="diagonal"
      speed={0.25}
      squareSize={64}
      borderColor="rgba(148, 163, 184, 0.34)"
      hoverFillColor="rgba(148, 163, 184, 0.16)"
      shape="square"
      hoverTrailAmount={5}
    />,
  );
};

if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", mountShapeGrid, { once: true });
} else {
  mountShapeGrid();
}
