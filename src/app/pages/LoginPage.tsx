import { type CSSProperties, useState } from "react";
import { zodResolver } from "@hookform/resolvers/zod";
import { Controller, useForm } from "react-hook-form";
import { Link, useLocation, useNavigate } from "react-router";
import { Eye, EyeOff } from "lucide-react";
import { Checkbox } from "../components/ui/checkbox";
import { Input } from "../components/ui/input";
import { Label } from "../components/ui/label";
import { getAuthErrorMessage } from "../auth/auth-api";
import { useLoginMutation, useLogoutMutation } from "../auth/auth-queries";
import { loginSchema, type LoginFormValues } from "../auth/auth-schemas";
import logo from "../../assets/LOGO.png";
import heroImage from "../../assets/hero-concert-bg.png";
import { AuthAdminBackground } from "./AuthAdminBackground";
import { AuthInfoCard } from "./AuthStudioVisualPanel";
import "./AuthPages.css";

type LoginPageProps = {
  variant?: "user" | "admin";
};

const pageCopy = {
  user: {
    title: "Welcome Back",
    description: "Welcome back! Enter your email and password to continue.",
    buttonIdle: "Sign in to Black Sky",
    buttonPending: "Signing in",
    visualTitle: "Welcome back! Please sign in to your Black Sky account",
    visualDescription:
      "Thank you for joining our live entertainment network. Activate your access, track upcoming shows, and keep your ticket journey close.",
    cardTitle: "Please enter your login details",
    cardDescription:
      "Stay connected with Black Sky Enterprise. Subscribe now for the latest concert updates and news.",
  },
  admin: {
    title: "Admin Login",
    description: "Masuk menggunakan akun admin Black Sky Enterprise.",
    buttonIdle: "Sign in to Admin",
    buttonPending: "Checking access",
    visualTitle: "Admin control room for Black Sky operations",
    visualDescription:
      "Akses internal untuk mengelola event, konten, vendor, dan monitoring operasional Black Sky Enterprise.",
    cardTitle: "Authorized personnel only",
    cardDescription:
      "Gunakan kredensial admin yang aktif. Akun user biasa tidak dapat masuk ke area admin.",
  },
} as const;

export function LoginPage({ variant = "user" }: LoginPageProps) {
  const navigate = useNavigate();
  const location = useLocation();
  const loginMutation = useLoginMutation();
  const logoutMutation = useLogoutMutation();
  const [showPassword, setShowPassword] = useState(false);
  const [submitError, setSubmitError] = useState("");
  const isAdminLogin = variant === "admin";
  const copy = pageCopy[variant];
  const isSubmitting = loginMutation.isPending || logoutMutation.isPending;
  const requestedRedirect = (location.state as { from?: unknown } | null)?.from;
  const memberRedirect =
    typeof requestedRedirect === "string" &&
    requestedRedirect.startsWith("/") &&
    !requestedRedirect.startsWith("//") &&
    !requestedRedirect.startsWith("/admin")
      ? requestedRedirect
      : "/dashboard";
  const form = useForm<LoginFormValues>({
    resolver: zodResolver(loginSchema),
    defaultValues: {
      email: "",
      password: "",
      remember: false,
    },
  });

  const onSubmit = form.handleSubmit(async (values) => {
    setSubmitError("");

    try {
      const user = await loginMutation.mutateAsync(values);

      if (isAdminLogin && !user.roles?.includes("admin")) {
        await logoutMutation.mutateAsync();
        setSubmitError("Akun ini tidak memiliki akses admin.");
        return;
      }

      if (!isAdminLogin && user.roles?.includes("admin")) {
        await logoutMutation.mutateAsync();
        setSubmitError("These credentials do not match our records.");
        return;
      }

      navigate(isAdminLogin ? "/admin" : memberRedirect);
    } catch (error) {
      setSubmitError(getAuthErrorMessage(error, "Login gagal."));
    }
  });

  const PasswordIcon = showPassword ? EyeOff : Eye;

  return (
    <main className="login-page">
      <AuthAdminBackground />
      <section className="login-page__form-side" aria-label="Login form">
        <div className="login-page__form-card">
          <a className="login-page__brand" href="/" aria-label="Black Sky Enterprise">
            <img src={logo} alt="" aria-hidden="true" />
          </a>

          <div className="login-page__intro">
            <h1>{copy.title}</h1>
            <p>{copy.description}</p>
          </div>

          <form className="auth-form login-page__form" onSubmit={onSubmit} noValidate>
            <div className="auth-form__field">
              <Label className="auth-form__label" htmlFor="login-email">
                Email address*
              </Label>
              <Input
                className="auth-form__input"
                id="login-email"
                type="email"
                autoComplete="email"
                placeholder="Enter your email address"
                aria-invalid={Boolean(form.formState.errors.email)}
                {...form.register("email")}
              />
              {form.formState.errors.email ? (
                <p className="auth-form__field-error">
                  {form.formState.errors.email.message}
                </p>
              ) : null}
            </div>

            <div className="auth-form__field">
              <Label className="auth-form__label" htmlFor="login-password">
                Password*
              </Label>
              <div className="auth-form__password-wrap">
                <Input
                  className="auth-form__input"
                  id="login-password"
                  type={showPassword ? "text" : "password"}
                  autoComplete="current-password"
                  placeholder="Password"
                  aria-invalid={Boolean(form.formState.errors.password)}
                  {...form.register("password")}
                />
                <button
                  className="auth-form__icon-button"
                  type="button"
                  aria-label={showPassword ? "Hide password" : "Show password"}
                  onClick={() => setShowPassword((current) => !current)}
                >
                  <PasswordIcon size={17} aria-hidden="true" />
                </button>
              </div>
              {form.formState.errors.password ? (
                <p className="auth-form__field-error">
                  {form.formState.errors.password.message}
                </p>
              ) : null}
            </div>

            <div className="auth-form__row">
              <Label className="auth-form__checkline" htmlFor="login-remember">
                <Controller
                  control={form.control}
                  name="remember"
                  render={({ field }) => (
                    <Checkbox
                      className="auth-form__checkbox"
                      id="login-remember"
                      checked={field.value}
                      onCheckedChange={(checked) => field.onChange(checked === true)}
                    />
                  )}
                />
                Remember Me
              </Label>
              <Link className="auth-form__link" to="/forgot-password">
                Forgot Password?
              </Link>
            </div>

            <p className="auth-form__alert" role="alert">
              {submitError}
            </p>

            <button
              className="auth-form__button"
              type="submit"
              disabled={isSubmitting}
            >
              {isSubmitting ? copy.buttonPending : copy.buttonIdle}
            </button>
          </form>

          <p className="auth-page__switch login-page__switch">
            {isAdminLogin ? (
              <Link to="/">Back to public site</Link>
            ) : (
              <>
                New on our platform? <Link to="/register">Create an account</Link>
              </>
            )}
          </p>
        </div>
      </section>

      <section
        className="login-page__visual-side"
        aria-label="Black Sky membership"
        style={{ "--login-visual-image": `url(${heroImage})` } as CSSProperties}
      >
        <div className="login-page__visual-panel">
          <div className="login-page__visual-copy">
            <h2>{copy.visualTitle}</h2>
            <p>{copy.visualDescription}</p>
          </div>

          <AuthInfoCard
            title={copy.cardTitle}
            description={copy.cardDescription}
          />
        </div>
      </section>
    </main>
  );
}
