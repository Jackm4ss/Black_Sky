import { zodResolver } from "@hookform/resolvers/zod";
import { useState } from "react";
import { useForm } from "react-hook-form";
import { Link } from "react-router";
import { Input } from "../components/ui/input";
import { Label } from "../components/ui/label";
import { getAuthErrorMessage } from "../auth/auth-api";
import { useForgotPasswordMutation } from "../auth/auth-queries";
import {
  forgotPasswordSchema,
  type ForgotPasswordFormValues,
} from "../auth/auth-schemas";
import logo from "../../assets/LOGO.png";
import heroImage from "../../assets/hero-concert-bg.png";
import { AuthAdminBackground } from "./AuthAdminBackground";
import { AuthStudioVisualPanel } from "./AuthStudioVisualPanel";
import "./AuthPages.css";

export function ForgotPasswordPage() {
  const forgotPasswordMutation = useForgotPasswordMutation();
  const [message, setMessage] = useState("");
  const [submitError, setSubmitError] = useState("");
  const form = useForm<ForgotPasswordFormValues>({
    resolver: zodResolver(forgotPasswordSchema),
    defaultValues: {
      email: "",
    },
  });

  const onSubmit = form.handleSubmit(async (values) => {
    setSubmitError("");
    setMessage("");

    try {
      const response = await forgotPasswordMutation.mutateAsync(values);
      setMessage(response.message);
    } catch (error) {
      setSubmitError(getAuthErrorMessage(error, "Gagal mengirim reset link."));
    }
  });

  return (
    <main className="login-page">
      <AuthAdminBackground />
      <section className="login-page__form-side" aria-label="Forgot password form">
        <div className="login-page__form-card login-page__form-card--compact login-page__form-card--forgot">
          <a className="login-page__brand" href="/" aria-label="Black Sky Enterprise">
            <img src={logo} alt="" aria-hidden="true" />
          </a>

          <div className="login-page__intro">
            <h1>Forgot Password?</h1>
            <p>No worries, we'll send you reset instructions.</p>
          </div>

          <form className="auth-form login-page__form" onSubmit={onSubmit} noValidate>
            <div className="auth-form__field">
              <Label className="auth-form__label" htmlFor="forgot-email">
                Email address*
              </Label>
              <Input
                className="auth-form__input"
                id="forgot-email"
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

            <p
              className={submitError ? "auth-form__alert" : "auth-form__success"}
              role={submitError ? "alert" : "status"}
            >
              {submitError || message}
              {message ? (
                <>
                  {" "}
                  <Link className="auth-form__link" to="/reset-password">
                    Open reset form
                  </Link>
                </>
              ) : null}
            </p>

            <button
              className="auth-form__button"
              type="submit"
              disabled={forgotPasswordMutation.isPending}
            >
              {forgotPasswordMutation.isPending ? "Sending" : "Send Reset Link"}
            </button>

            <Link className="auth-form__button auth-form__button--ghost" to="/login">
              Back to login
            </Link>
          </form>
        </div>
      </section>

      <AuthStudioVisualPanel
        ariaLabel="Black Sky password recovery"
        image={heroImage}
        title="Don't worry it happens! Resetting your password is quick and easy."
        description="Just enter your registered email address below, and we'll send you a secure link to reset your password. Follow the instructions in the email, and you'll be back in your account in no time!"
        cardTitle="Follow the instructions"
        cardDescription="If you don't see the email in your inbox, be sure to check your spam or junk folder."
      />
    </main>
  );
}
