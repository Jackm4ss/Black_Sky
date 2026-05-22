import { http, getRequestErrorMessage } from "../lib/http";
import {
  clearCurrentSession,
  createLoginSession,
  createRegisterSession,
  getCurrentSession,
  requestPasswordReset as requestMockPasswordReset,
  resetPassword as resetMockPassword,
} from "./session";
import type {
  ForgotPasswordFormValues,
  LoginFormValues,
  RegisterFormValues,
  ResetPasswordFormValues,
} from "./auth-schemas";
import { getRegistrationAttribution } from "./registration-attribution";

const authDriver = import.meta.env.VITE_AUTH_DRIVER ?? "sanctum";
const usesSanctum = authDriver === "sanctum";

export type AuthUser = {
  id?: number | string;
  name?: string;
  email: string;
  emailVerifiedAt?: string | null;
  phone?: string | null;
  avatar?: string | null;
  countryCode?: string | null;
  registrationSource?: string | null;
  dateOfBirth?: string | null;
  gender?: string | null;
  roles?: string[];
};

export type ResetPasswordPayload = ResetPasswordFormValues & {
  email?: string | null;
  token?: string | null;
};

export type AuthStatusResponse = {
  message: string;
};

type LaravelUserPayload =
  | AuthUser
  | {
      data?: AuthUser;
      user?: AuthUser;
    };

export const toAuthUser = (payload: LaravelUserPayload): AuthUser => {
  const source =
    "data" in payload && payload.data
      ? payload.data
      : "user" in payload && payload.user
        ? payload.user
        : payload;

  return {
    id: source.id,
    name: source.name,
    email: source.email,
    phone: source.phone ?? null,
    avatar: source.avatar ?? null,
    countryCode:
      source.countryCode ??
      (source as AuthUser & { country_code?: string | null }).country_code ??
      null,
    registrationSource:
      source.registrationSource ??
      (source as AuthUser & { registration_source?: string | null })
        .registration_source ??
      null,
    dateOfBirth:
      source.dateOfBirth ??
      (source as AuthUser & { date_of_birth?: string | null }).date_of_birth ??
      null,
    gender: source.gender ?? null,
    roles: source.roles ?? [],
    emailVerifiedAt:
      source.emailVerifiedAt ??
      (source as AuthUser & { email_verified_at?: string | null }).email_verified_at ??
      null,
  };
};

const getMockUser = (): AuthUser | null => {
  const session = getCurrentSession();

  if (!session) return null;

    return {
      name: session.name,
      email: session.email,
      phone: session.phone,
      avatar: session.avatar,
      roles: [],
      emailVerifiedAt: session.signedInAt,
    };
  };

const ensureCsrfCookie = async () => {
  await http.get("/sanctum/csrf-cookie");
};

export const authApi = {
  async currentUser(): Promise<AuthUser | null> {
    if (!usesSanctum) {
      return getMockUser();
    }

    try {
      const response = await http.get<LaravelUserPayload>("/api/user");
      return toAuthUser(response.data);
    } catch (error) {
      if (
        typeof error === "object" &&
        error !== null &&
        "response" in error &&
        (error as { response?: { status?: number } }).response?.status === 401
      ) {
        return null;
      }

      throw new Error(getRequestErrorMessage(error, "Gagal mengambil data user."));
    }
  },

  async login(values: LoginFormValues): Promise<AuthUser> {
    if (!usesSanctum) {
      const session = await createLoginSession(values);
      return toAuthUser({
        name: session.name,
        email: session.email,
        phone: session.phone,
        avatar: session.avatar,
        roles: [],
        emailVerifiedAt: session.signedInAt,
      });
    }

    try {
      await ensureCsrfCookie();
      await http.post("/api/login", {
        email: values.email,
        password: values.password,
        remember: values.remember,
      });

      const user = await authApi.currentUser();
      if (!user) throw new Error("Login berhasil, tetapi data user belum tersedia.");

      return user;
    } catch (error) {
      throw new Error(getRequestErrorMessage(error, "Login gagal."));
    }
  },

  async register(values: RegisterFormValues): Promise<AuthUser> {
    if (!usesSanctum) {
      const session = await createRegisterSession(values);
      return toAuthUser({
        name: session.name,
        email: session.email,
        phone: session.phone,
        avatar: session.avatar,
        roles: ["user"],
        emailVerifiedAt: session.signedInAt,
      });
    }

    try {
      const attribution = getRegistrationAttribution();

      await ensureCsrfCookie();
      await http.post("/api/register", {
        name: values.name,
        email: values.email,
        country_code: values.countryCode,
        registration_source: attribution.source,
        registration_referrer: attribution.referrer,
        password: values.password,
        password_confirmation: values.password,
        terms: values.acceptedTerms,
      });

      const user = await authApi.currentUser();
      if (!user) throw new Error("Registrasi berhasil, tetapi data user belum tersedia.");

      return user;
    } catch (error) {
      throw new Error(getRequestErrorMessage(error, "Registrasi gagal."));
    }
  },

  async forgotPassword(values: ForgotPasswordFormValues): Promise<AuthStatusResponse> {
    if (!usesSanctum) {
      const email = await requestMockPasswordReset(values);
      return { message: `Reset instructions sent to ${email}.` };
    }

    try {
      await ensureCsrfCookie();
      const response = await http.post<AuthStatusResponse>("/api/forgot-password", {
        email: values.email,
      });

      return {
        message: response.data.message ?? "Reset instructions sent.",
      };
    } catch (error) {
      throw new Error(getRequestErrorMessage(error, "Gagal mengirim reset link."));
    }
  },

  async resetPassword(values: ResetPasswordPayload): Promise<AuthStatusResponse> {
    if (!usesSanctum) {
      await resetMockPassword(values);
      return { message: "Password updated. You can sign in now." };
    }

    try {
      await ensureCsrfCookie();
      const response = await http.post<AuthStatusResponse>("/api/reset-password", {
        email: values.email,
        token: values.token,
        password: values.password,
        password_confirmation: values.passwordConfirmation,
      });

      return {
        message: response.data.message ?? "Password updated. You can sign in now.",
      };
    } catch (error) {
      throw new Error(getRequestErrorMessage(error, "Reset password gagal."));
    }
  },

  async resendEmailVerification(): Promise<AuthStatusResponse> {
    if (!usesSanctum) {
      return { message: "Verification email sent." };
    }

    try {
      await ensureCsrfCookie();
      const response = await http.post<AuthStatusResponse>(
        "/api/email/verification-notification",
      );

      return {
        message: response.data.message ?? "Verification email sent.",
      };
    } catch (error) {
      throw new Error(
        getRequestErrorMessage(error, "Gagal mengirim email verifikasi."),
      );
    }
  },

  async logout(): Promise<void> {
    if (!usesSanctum) {
      clearCurrentSession();
      return;
    }

    try {
      await http.post("/api/v1/logout");
    } catch (error) {
      throw new Error(getRequestErrorMessage(error, "Logout gagal."));
    }
  },
};

export const getAuthErrorMessage = getRequestErrorMessage;
