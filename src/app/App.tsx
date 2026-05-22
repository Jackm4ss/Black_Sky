import { lazy, Suspense, useEffect } from "react";
import { Navigate, Route, Routes, useLocation, useParams } from "react-router";
import { rememberRegistrationAttribution } from "./auth/registration-attribution";
import { RequireAuth } from "./routes/RequireAuth";
import { ScrollToTop } from "./routes/ScrollToTop";

const LandingPage = lazy(() =>
  import("./pages/LandingPage").then((module) => ({ default: module.LandingPage })),
);
const DiscoverPage = lazy(() =>
  import("./pages/DiscoverPage").then((module) => ({ default: module.DiscoverPage })),
);
const EventDetailPage = lazy(() =>
  import("./pages/EventDetailPage").then((module) => ({ default: module.EventDetailPage })),
);
const BlogPage = lazy(() =>
  import("./pages/BlogPage").then((module) => ({ default: module.BlogPage })),
);
const BlogPostPage = lazy(() =>
  import("./pages/BlogPostPage").then((module) => ({ default: module.BlogPostPage })),
);
const PortfolioDetailPage = lazy(() =>
  import("./pages/PortfolioDetailPage").then((module) => ({
    default: module.PortfolioDetailPage,
  })),
);
const LoginPage = lazy(() =>
  import("./pages/LoginPage").then((module) => ({ default: module.LoginPage })),
);
const RegisterPage = lazy(() =>
  import("./pages/RegisterPage").then((module) => ({ default: module.RegisterPage })),
);
const ForgotPasswordPage = lazy(() =>
  import("./pages/ForgotPasswordPage").then((module) => ({
    default: module.ForgotPasswordPage,
  })),
);
const ResetPasswordPage = lazy(() =>
  import("./pages/ResetPasswordPage").then((module) => ({
    default: module.ResetPasswordPage,
  })),
);
const EmailVerificationPage = lazy(() =>
  import("./pages/EmailVerificationPage").then((module) => ({
    default: module.EmailVerificationPage,
  })),
);
const LoginSuccessPage = lazy(() =>
  import("./pages/LoginSuccessPage").then((module) => ({
    default: module.LoginSuccessPage,
  })),
);
const UserDashboardPage = lazy(() =>
  import("./pages/UserDashboardPage").then((module) => ({
    default: module.UserDashboardPage,
  })),
);

function RouteFallback() {
  return (
    <div
      aria-hidden="true"
      style={{
        minHeight: "100vh",
        background: "#050505",
      }}
    />
  );
}

function LegacyBlogPostRedirect() {
  const { slug = "" } = useParams();

  return <Navigate to={`/news/${slug}`} replace />;
}

export default function App() {
  const location = useLocation();

  useEffect(() => {
    rememberRegistrationAttribution();
  }, [location.search]);

  return (
    <div
      style={{
        background: "#050505",
        minHeight: "100vh",
        fontFamily: "'Inter', sans-serif",
        scrollBehavior: "smooth",
      }}
    >
      {/* Global Scrollbar Styling */}
      <style>{`
        * {
          scrollbar-width: thin;
          scrollbar-color: rgba(168,85,247,0.3) transparent;
        }
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
        ::selection {
          background: rgba(168,85,247,0.35);
          color: #fff;
        }
        html {
          scroll-behavior: smooth;
        }
      `}</style>

      <ScrollToTop />
      <Suspense fallback={<RouteFallback />}>
        <Routes>
          <Route path="/" element={<LandingPage />} />
          <Route path="/discover" element={<DiscoverPage />} />
          <Route path="/events" element={<DiscoverPage />} />
          <Route path="/events/:slug" element={<EventDetailPage />} />
          <Route path="/portfolio/:slug" element={<PortfolioDetailPage />} />
          <Route path="/projects/:slug" element={<PortfolioDetailPage />} />
          <Route path="/news" element={<BlogPage />} />
          <Route path="/news/:slug" element={<BlogPostPage />} />
          <Route path="/blog" element={<Navigate to="/news" replace />} />
          <Route path="/blog/:slug" element={<LegacyBlogPostRedirect />} />
          <Route path="/login" element={<LoginPage />} />
          <Route path="/register" element={<RegisterPage />} />
          <Route path="/forgot-password" element={<ForgotPasswordPage />} />
          <Route path="/reset-password" element={<ResetPasswordPage />} />
          <Route
            path="/email-verification"
            element={
              <RequireAuth>
                <EmailVerificationPage />
              </RequireAuth>
            }
          />
          <Route
            path="/login/success"
            element={
              <RequireAuth>
                <LoginSuccessPage />
              </RequireAuth>
            }
          />
          <Route
            path="/dashboard"
            element={
              <RequireAuth allowedRoles={["user"]}>
                <UserDashboardPage />
              </RequireAuth>
            }
          />
          <Route
            path="/dashboard/account"
            element={
              <RequireAuth allowedRoles={["user"]}>
                <UserDashboardPage />
              </RequireAuth>
            }
          />
          <Route
            path="/dashboard/saved-events"
            element={
              <RequireAuth allowedRoles={["user"]}>
                <UserDashboardPage />
              </RequireAuth>
            }
          />
          <Route
            path="/dashboard/notifications"
            element={
              <RequireAuth allowedRoles={["user"]}>
                <UserDashboardPage />
              </RequireAuth>
            }
          />
          <Route
            path="/dashboard/support"
            element={
              <RequireAuth allowedRoles={["user"]}>
                <UserDashboardPage />
              </RequireAuth>
            }
          />
          <Route
            path="/dashboard/security"
            element={
              <RequireAuth allowedRoles={["user"]}>
                <UserDashboardPage />
              </RequireAuth>
            }
          />
          <Route
            path="/dashboard/password"
            element={
              <RequireAuth allowedRoles={["user"]}>
                <UserDashboardPage />
              </RequireAuth>
            }
          />
          <Route
            path="/dashboard/remove-account"
            element={
              <RequireAuth allowedRoles={["user"]}>
                <UserDashboardPage />
              </RequireAuth>
            }
          />
          <Route path="/reset-password/:token" element={<ResetPasswordPage />} />
          <Route path="*" element={<Navigate to="/" replace />} />
        </Routes>
      </Suspense>
    </div>
  );
}
