import { useState } from "react";
import { zodResolver } from "@hookform/resolvers/zod";
import { useForm, type UseFormRegisterReturn } from "react-hook-form";
import { Link, useParams, useSearchParams } from "react-router";
import { Eye, EyeOff } from "lucide-react";
import { Input } from "../components/ui/input";
import { Label } from "../components/ui/label";
import { getAuthErrorMessage } from "../auth/auth-api";
import { useResetPasswordMutation } from "../auth/auth-queries";
import { resetPasswordSchema, type ResetPasswordFormValues } from "../auth/auth-schemas";
import logo from "../../assets/LOGO.png";
import heroImage from "../../assets/hero-concert-bg.png";
import { AuthAdminBackground } from "./AuthAdminBackground";
import { AuthStudioVisualPanel } from "./AuthStudioVisualPanel";
import "./AuthPages.css";

type PasswordFieldProps = {
  id: string;
  label: string;
  autoComplete: string;
  error?: string;
  isVisible: boolean;
  registration: UseFormRegisterReturn;
  onToggle: () => void;
};

const PasswordField = ({
  id,
  label,
  autoComplete,
  error,
  isVisible,
  registration,
  onToggle,
}: PasswordFieldProps) => {
  const Icon = isVisible ? EyeOff : Eye;

  return (
    <div className="auth-form__field">
      <Label className="auth-form__label" htmlFor={id}>
        {label}
      </Label>
      <div className="auth-form__password-wrap">
        <Input
          className="auth-form__input"
          id={id}
          type={isVisible ? "text" : "password"}
          autoComplete={autoComplete}
          placeholder="Password"
          aria-invalid={Boolean(error)}
          {...registration}
        />
        <button
          className="auth-form__icon-button"
          type="button"
          aria-label={isVisible ? "Hide password" : "Show password"}
          onClick={onToggle}
        >
          <Icon size={17} aria-hidden="true" />
        </button>
      </div>
      {error ? <p className="auth-form__field-error">{error}</p> : null}
    </div>
  );
};

export function ResetPasswordPage() {
  const [searchParams] = useSearchParams();
  const { token: routeToken } = useParams();
  const resetPasswordMutation = useResetPasswordMutation();
  const [showPassword, setShowPassword] = useState(false);
  const [showPasswordConfirmation, setShowPasswordConfirmation] = useState(false);
  const [submitError, setSubmitError] = useState("");
  const [message, setMessage] = useState("");
  const form = useForm<ResetPasswordFormValues>({
    resolver: zodResolver(resetPasswordSchema),
    defaultValues: {
      email: searchParams.get("email") ?? "",
      password: "",
      passwordConfirmation: "",
    },
  });

  const onSubmit = form.handleSubmit(async (values) => {
    setSubmitError("");
    setMessage("");

    try {
      const response = await resetPasswordMutation.mutateAsync({
        ...values,
        email: values.email,
        token: routeToken ?? searchParams.get("token"),
      });

      setMessage(response.message);
      form.reset();
    } catch (error) {
      setSubmitError(getAuthErrorMessage(error, "Reset password gagal."));
    }
  });

  return (
    <main className="login-page">
      <AuthAdminBackground />
      <section className="login-page__form-side" aria-label="Reset password form">
        <div className="login-page__form-card login-page__form-card--reset">
          <a className="login-page__brand" href="/" aria-label="Black Sky Enterprise">
            <img src={logo} alt="" aria-hidden="true" />
          </a>

          <div className="login-page__intro">
            <h1>Set new password</h1>
            <p>Must be at least 8 characters.</p>
          </div>

          <form className="auth-form login-page__form" onSubmit={onSubmit} noValidate>
            <div className="auth-form__field">
              <Label className="auth-form__label" htmlFor="reset-email">
                Email address*
              </Label>
              <Input
                className="auth-form__input"
                id="reset-email"
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

            <PasswordField
              id="reset-password"
              label="New Password*"
              autoComplete="new-password"
              error={form.formState.errors.password?.message}
              isVisible={showPassword}
              registration={form.register("password")}
              onToggle={() => setShowPassword((current) => !current)}
            />

            <PasswordField
              id="reset-password-confirmation"
              label="Confirm Password*"
              autoComplete="new-password"
              error={form.formState.errors.passwordConfirmation?.message}
              isVisible={showPasswordConfirmation}
              registration={form.register("passwordConfirmation")}
              onToggle={() => setShowPasswordConfirmation((current) => !current)}
            />

            <p
              className={submitError ? "auth-form__alert" : "auth-form__success"}
              role={submitError ? "alert" : "status"}
            >
              {submitError || message}
            </p>

            <button
              className="auth-form__button"
              type="submit"
              disabled={resetPasswordMutation.isPending}
            >
              {resetPasswordMutation.isPending ? "Resetting" : "Reset Password"}
            </button>

            <Link className="auth-form__button auth-form__button--ghost" to="/login">
              Back to login
            </Link>
          </form>
        </div>
      </section>

      <AuthStudioVisualPanel
        ariaLabel="Black Sky password reset"
        image={heroImage}
        title="You're just one step away from regaining access to your account."
        description="Please create a new password below. Make sure it's strong and secure, combining letters, numbers, and special characters."
        cardTitle="Reset Your Password"
        cardDescription="You're just one step away from regaining access to your account."
      />
    </main>
  );
}
