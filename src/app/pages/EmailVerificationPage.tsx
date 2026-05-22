import { useState } from "react";
import { Link } from "react-router";
import { getAuthErrorMessage } from "../auth/auth-api";
import {
  useCurrentUser,
  useResendEmailVerificationMutation,
} from "../auth/auth-queries";
import logo from "../../assets/LOGO.png";
import heroImage from "../../assets/hero-concert-bg.png";
import { AuthAdminBackground } from "./AuthAdminBackground";
import { AuthStudioVisualPanel } from "./AuthStudioVisualPanel";
import "./AuthPages.css";

export function EmailVerificationPage() {
  const { data: user } = useCurrentUser();
  const resendMutation = useResendEmailVerificationMutation();
  const [message, setMessage] = useState("");
  const [submitError, setSubmitError] = useState("");

  const handleResend = async () => {
    setSubmitError("");
    setMessage("");

    try {
      const response = await resendMutation.mutateAsync();
      setMessage(response.message);
    } catch (error) {
      setSubmitError(getAuthErrorMessage(error, "Gagal mengirim email verifikasi."));
    }
  };

  return (
    <main className="login-page">
      <AuthAdminBackground />
      <section className="login-page__form-side" aria-label="Email verification">
        <div className="login-page__form-card login-page__form-card--compact login-page__form-card--verify">
          <a className="login-page__brand" href="/" aria-label="Black Sky Enterprise">
            <img src={logo} alt="" aria-hidden="true" />
          </a>

          <div className="login-page__intro">
            <h1>Verify your email</h1>
            <p>
              We sent a verification link to {user?.email ?? "your email address"}.
            </p>
          </div>

          <div className="auth-form login-page__form">
            <p
              className={submitError ? "auth-form__alert" : "auth-form__success"}
              role={submitError ? "alert" : "status"}
            >
              {submitError || message}
            </p>

            <button
              className="auth-form__button"
              type="button"
              disabled={resendMutation.isPending}
              onClick={handleResend}
            >
              {resendMutation.isPending ? "Sending" : "Resend verification"}
            </button>

            <Link className="auth-form__button auth-form__button--ghost" to="/dashboard">
              Continue
            </Link>
          </div>
        </div>
      </section>

      <AuthStudioVisualPanel
        ariaLabel="Black Sky email verification"
        image={heroImage}
        title="Confirm your email before entering the Black Sky member area."
        description="Email verification protects ticket access, order history, event alerts, and account recovery for every member."
        cardTitle="Check your inbox"
        cardDescription="Open the verification email from Black Sky, then return here to continue your account setup."
      />
    </main>
  );
}
