import { useEffect, useState } from "react";
import { useNavigate } from "react-router";
import { getAuthErrorMessage, isAuthUserEmailVerified } from "../auth/auth-api";
import {
  useCurrentUser,
  useResendEmailVerificationMutation,
} from "../auth/auth-queries";
import logo from "../../assets/LOGO.png";
import heroImage from "../../assets/hero-concert-bg.png";
import { AuthAdminBackground } from "./AuthAdminBackground";
import { AuthStudioVisualPanel } from "./AuthStudioVisualPanel";
import "./AuthPages.css";

const RESEND_COOLDOWN_SECONDS = 35;

const getRetryAfterSeconds = (error: unknown) => {
  if (typeof error !== "object" || error === null || !("response" in error)) {
    return null;
  }

  const response = (error as {
    response?: {
      status?: number;
      headers?: Record<string, string | number | string[] | undefined>;
    };
  }).response;

  if (response?.status !== 429) {
    return null;
  }

  const retryAfterHeader =
    response.headers?.["retry-after"] ?? response.headers?.["Retry-After"];
  const retryAfter = Array.isArray(retryAfterHeader)
    ? Number(retryAfterHeader[0])
    : Number(retryAfterHeader);

  return Number.isFinite(retryAfter) && retryAfter > 0
    ? Math.ceil(retryAfter)
    : RESEND_COOLDOWN_SECONDS;
};

export function EmailVerificationPage() {
  const navigate = useNavigate();
  const { data: user } = useCurrentUser();
  const resendMutation = useResendEmailVerificationMutation();
  const [message, setMessage] = useState("");
  const [submitError, setSubmitError] = useState("");
  const [resendAvailableAt, setResendAvailableAt] = useState(0);
  const [nowMs, setNowMs] = useState(() => Date.now());
  const resendCooldownKey = user?.email
    ? `black-sky:verification-resend:${user.email}`
    : "";
  const resendCooldownSeconds = Math.max(
    0,
    Math.ceil((resendAvailableAt - nowMs) / 1000),
  );
  const resendIsCoolingDown = resendCooldownSeconds > 0;

  useEffect(() => {
    if (isAuthUserEmailVerified(user)) {
      navigate("/dashboard", { replace: true });
    }
  }, [navigate, user]);

  useEffect(() => {
    if (!resendCooldownKey) {
      setResendAvailableAt(0);
      return;
    }

    const currentTime = Date.now();
    const storedAvailableAt = Number(
      window.localStorage.getItem(resendCooldownKey) ?? 0,
    );

    setNowMs(currentTime);

    if (Number.isFinite(storedAvailableAt) && storedAvailableAt > currentTime) {
      setResendAvailableAt(storedAvailableAt);
      return;
    }

    setResendAvailableAt(0);
  }, [resendCooldownKey]);

  useEffect(() => {
    if (!resendIsCoolingDown) return;

    const timer = window.setInterval(() => {
      setNowMs(Date.now());
    }, 1000);

    return () => window.clearInterval(timer);
  }, [resendIsCoolingDown]);

  const startResendCooldown = (seconds = RESEND_COOLDOWN_SECONDS) => {
    const currentTime = Date.now();
    const availableAt = currentTime + seconds * 1000;

    setNowMs(currentTime);
    setResendAvailableAt(availableAt);

    if (resendCooldownKey) {
      window.localStorage.setItem(resendCooldownKey, String(availableAt));
    }
  };

  const handleResend = async () => {
    setSubmitError("");
    setMessage("");

    if (resendIsCoolingDown) {
      setMessage(`You can resend verification in ${resendCooldownSeconds}s.`);
      return;
    }

    try {
      const response = await resendMutation.mutateAsync();
      startResendCooldown();
      setMessage(response.message);
    } catch (error) {
      const retryAfterSeconds = getRetryAfterSeconds(error);

      if (retryAfterSeconds) {
        startResendCooldown(retryAfterSeconds);
      }

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
              disabled={resendMutation.isPending || resendIsCoolingDown}
              onClick={handleResend}
            >
              {resendMutation.isPending
                ? "Sending"
                : resendIsCoolingDown
                  ? `Resend in ${resendCooldownSeconds}s`
                  : "Resend verification"}
            </button>

          </div>
        </div>
      </section>

      <AuthStudioVisualPanel
        ariaLabel="Black Sky email verification"
        image={heroImage}
        title="Confirm your email before entering the Black Sky member area."
        description="Email verification protects ticket access, order history, event alerts, and account recovery for every member."
        cardTitle="Check your inbox"
        cardDescription="Open the verification email from Black Sky. Once verified, your account can enter the member dashboard."
      />
    </main>
  );
}
