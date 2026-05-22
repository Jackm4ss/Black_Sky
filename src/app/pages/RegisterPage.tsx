import { type CSSProperties, useState } from "react";
import { zodResolver } from "@hookform/resolvers/zod";
import { Controller, useForm } from "react-hook-form";
import { Link, useNavigate } from "react-router";
import { Eye, EyeOff } from "lucide-react";
import { Checkbox } from "../components/ui/checkbox";
import {
  Dialog,
  DialogClose,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from "../components/ui/dialog";
import { Input } from "../components/ui/input";
import { Label } from "../components/ui/label";
import { RegistrationCountryDropdown } from "../components/RegistrationCountryDropdown";
import { getAuthErrorMessage } from "../auth/auth-api";
import { useRegisterMutation } from "../auth/auth-queries";
import { registerSchema, type RegisterFormValues } from "../auth/auth-schemas";
import logo from "../../assets/LOGO.png";
import heroImage from "../../assets/hero-concert-bg.png";
import { AuthAdminBackground } from "./AuthAdminBackground";
import { AuthInfoCard } from "./AuthStudioVisualPanel";
import "./AuthPages.css";

export function RegisterPage() {
  const navigate = useNavigate();
  const registerMutation = useRegisterMutation();
  const [showPassword, setShowPassword] = useState(false);
  const [termsOpen, setTermsOpen] = useState(false);
  const [submitError, setSubmitError] = useState("");
  const form = useForm<RegisterFormValues>({
    resolver: zodResolver(registerSchema),
    defaultValues: {
      name: "",
      email: "",
      countryCode: "",
      password: "",
      acceptedTerms: false,
    },
  });

  const onSubmit = form.handleSubmit(async (values) => {
    setSubmitError("");

    try {
      await registerMutation.mutateAsync(values);
      navigate("/email-verification");
    } catch (error) {
      setSubmitError(getAuthErrorMessage(error, "Registrasi gagal."));
    }
  });

  const PasswordIcon = showPassword ? EyeOff : Eye;

  return (
    <main className="login-page">
      <AuthAdminBackground />
      <section className="login-page__form-side" aria-label="Register form">
        <div className="login-page__form-card login-page__form-card--register">
          <a className="login-page__brand" href="/" aria-label="Black Sky Enterprise">
            <img src={logo} alt="" aria-hidden="true" />
          </a>

          <div className="login-page__intro">
            <h1>Create an account</h1>
            <p>Let's get started. Fill in the details below to create your account.</p>
          </div>

          <form className="auth-form login-page__form" onSubmit={onSubmit} noValidate>
            <div className="auth-form__field">
              <Label className="auth-form__label" htmlFor="register-name">
                Name*
              </Label>
              <Input
                className="auth-form__input"
                id="register-name"
                type="text"
                autoComplete="name"
                placeholder="Enter your full name"
                aria-invalid={Boolean(form.formState.errors.name)}
                {...form.register("name")}
              />
              {form.formState.errors.name ? (
                <p className="auth-form__field-error">
                  {form.formState.errors.name.message}
                </p>
              ) : null}
            </div>

            <div className="auth-form__field">
              <Label className="auth-form__label" htmlFor="register-email">
                Email address*
              </Label>
              <Input
                className="auth-form__input"
                id="register-email"
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
              <Label className="auth-form__label" htmlFor="register-country">
                Country*
              </Label>
              <Controller
                control={form.control}
                name="countryCode"
                render={({ field }) => (
                  <RegistrationCountryDropdown
                    id="register-country"
                    value={field.value}
                    onChange={field.onChange}
                    invalid={Boolean(form.formState.errors.countryCode)}
                  />
                )}
              />
              {form.formState.errors.countryCode ? (
                <p className="auth-form__field-error">
                  {form.formState.errors.countryCode.message}
                </p>
              ) : null}
            </div>

            <div className="auth-form__field">
              <Label className="auth-form__label" htmlFor="register-password">
                Password*
              </Label>
              <div className="auth-form__password-wrap">
                <Input
                  className="auth-form__input"
                  id="register-password"
                  type={showPassword ? "text" : "password"}
                  autoComplete="new-password"
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
              ) : (
                <p className="auth-form__hint">Minimum 8 characters.</p>
              )}
            </div>

            <div className="auth-form__row login-page__terms-row">
              <Label className="auth-form__checkline" htmlFor="register-terms">
                <Controller
                  control={form.control}
                  name="acceptedTerms"
                  render={({ field }) => (
                    <Checkbox
                      className="auth-form__checkbox"
                      id="register-terms"
                      checked={field.value}
                      onCheckedChange={(checked) => field.onChange(checked === true)}
                      aria-invalid={Boolean(form.formState.errors.acceptedTerms)}
                    />
                  )}
                />
                <span>
                  I agree to the{" "}
                  <a
                    className="auth-form__link"
                    href="#terms"
                    onClick={(event) => {
                      event.preventDefault();
                      event.stopPropagation();
                      setTermsOpen(true);
                    }}
                  >
                    Terms & Conditions
                  </a>
                </span>
              </Label>
            </div>
            {form.formState.errors.acceptedTerms ? (
              <p className="auth-form__field-error">
                {form.formState.errors.acceptedTerms.message}
              </p>
            ) : null}

            <p className="auth-form__alert" role="alert">
              {submitError}
            </p>

            <button
              className="auth-form__button"
              type="submit"
              disabled={registerMutation.isPending}
            >
              {registerMutation.isPending ? "Signing up" : "Create Black Sky account"}
            </button>
          </form>

          <p className="auth-page__switch login-page__switch">
            Already have account? <Link to="/login">Sign in</Link>
          </p>
        </div>
      </section>

      <section
        className="login-page__visual-side"
        aria-label="Black Sky registration"
        style={{ "--login-visual-image": `url(${heroImage})` } as CSSProperties}
      >
        <div className="login-page__visual-panel">
          <div className="login-page__visual-copy">
            <h2>Create your Black Sky account and join the show</h2>
            <p>
              Register once to unlock ticket history, event alerts, and curated access
              for upcoming live experiences across Southeast Asia.
            </p>
          </div>

          <AuthInfoCard
            title="Start your member access"
            description="Stay connected with Black Sky Enterprise. Get first looks at new concerts, venue updates, and ticket releases."
          />
        </div>
      </section>

      <Dialog open={termsOpen} onOpenChange={setTermsOpen}>
        <DialogContent className="auth-terms-dialog">
          <DialogHeader>
            <DialogTitle className="auth-terms-dialog__title">
              Terms &amp; Conditions
            </DialogTitle>
            <DialogDescription className="auth-terms-dialog__description">
              Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer
              posuere erat a ante venenatis dapibus posuere velit aliquet. Cras
              mattis consectetur purus sit amet fermentum.
            </DialogDescription>
          </DialogHeader>
          <div className="auth-terms-dialog__body">
            <p>
              Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed posuere
              consectetur est at lobortis. Aenean lacinia bibendum nulla sed
              consectetur. Maecenas faucibus mollis interdum.
            </p>
            <p>
              Donec ullamcorper nulla non metus auctor fringilla. Vestibulum id
              ligula porta felis euismod semper. Praesent commodo cursus magna,
              vel scelerisque nisl consectetur et.
            </p>
          </div>
          <DialogFooter>
            <DialogClose asChild>
              <button className="auth-terms-dialog__button" type="button">
                Close
              </button>
            </DialogClose>
          </DialogFooter>
        </DialogContent>
      </Dialog>
    </main>
  );
}
