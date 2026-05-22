import ShapeGrid from "../components/ShapeGrid";

export function AuthAdminBackground() {
  return (
    <div className="login-page__shape-grid" aria-hidden="true">
      <ShapeGrid
        direction="diagonal"
        speed={0.25}
        squareSize={64}
        borderColor="rgba(148, 163, 184, 0.34)"
        hoverFillColor="rgba(148, 163, 184, 0.16)"
        shape="square"
        hoverTrailAmount={5}
      />
    </div>
  );
}
