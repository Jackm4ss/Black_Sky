import { useEffect, useMemo, useRef, useState } from "react";
import { zodResolver } from "@hookform/resolvers/zod";
import { Controller, useForm } from "react-hook-form";
import { Link, useLocation, useNavigate } from "react-router";
import { z } from "zod";
import {
  ArrowLeft,
  ArrowRight,
  Bell,
  Camera,
  ChevronRight,
  Heart,
  LifeBuoy,
  LogOut,
  ShieldCheck,
  Ticket,
  Trash2,
  Upload,
  UserRound,
} from "lucide-react";
import { getAuthErrorMessage } from "../auth/auth-api";
import { useLogoutMutation } from "../auth/auth-queries";
import { Footer } from "../components/Footer";
import { EventSlideCard, mapPublicEventToSlide } from "../components/EventsSection";
import { Navbar } from "../components/Navbar";
import { RegistrationCountryDropdown } from "../components/RegistrationCountryDropdown";
import { Input } from "../components/ui/input";
import { Label } from "../components/ui/label";
import { PhoneInput } from "../components/ui/phone-input";
import type { MemberNotification, MemberTicket } from "../user-dashboard/user-dashboard-api";
import {
  useMemberDashboard,
  useDeleteMemberAccount,
  useUpdateMemberAccount,
  useUpdateMemberPassword,
} from "../user-dashboard/user-dashboard-queries";
import "./UserDashboardPage.css";

const accountSchema = z.object({
  fullName: z.string().min(2, "Full name must be at least 2 characters.").max(180, "Full name is too long."),
  email: z.string().email("Enter a valid email address."),
  phone: z.string().max(20, "Phone number is too long.").optional(),
  countryCode: z.string().max(2).optional(),
  dateOfBirth: z.string().optional(),
  gender: z.enum(["", "male", "female", "non_binary", "prefer_not_to_say"]),
});

const passwordSchema = z
  .object({
    currentPassword: z.string().min(1, "Current password is required."),
    password: z.string().min(8, "Password must be at least 8 characters."),
    passwordConfirmation: z.string().min(1, "Confirm your new password."),
  })
  .refine((value) => value.password === value.passwordConfirmation, {
      message: "New password confirmation does not match.",
    path: ["passwordConfirmation"],
  });

const removeAccountSchema = z.object({
  password: z.string().min(1, "Password is required to remove your account."),
});

type AccountFormValues = z.infer<typeof accountSchema>;
type PasswordFormValues = z.infer<typeof passwordSchema>;
type RemoveAccountFormValues = z.infer<typeof removeAccountSchema>;
type DashboardView =
  | "overview"
  | "account"
  | "saved-events"
  | "notifications"
  | "password"
  | "support"
  | "remove-account";

type MenuItem = {
  view?: DashboardView;
  href: string;
  label: string;
  description: string;
  Icon: typeof UserRound;
};

type BirthDateParts = {
  day: string;
  month: string;
  year: string;
};

const BIRTH_YEAR_START = 1900;
const now = new Date();
const currentYear = now.getFullYear();
const currentMonth = now.getMonth() + 1;
const currentDay = now.getDate();

const birthYearOptions = Array.from({ length: currentYear - BIRTH_YEAR_START + 1 }, (_, index) =>
  String(currentYear - index),
);

const birthMonthOptions = [
  { value: "01", label: "January" },
  { value: "02", label: "February" },
  { value: "03", label: "March" },
  { value: "04", label: "April" },
  { value: "05", label: "May" },
  { value: "06", label: "June" },
  { value: "07", label: "July" },
  { value: "08", label: "August" },
  { value: "09", label: "September" },
  { value: "10", label: "October" },
  { value: "11", label: "November" },
  { value: "12", label: "December" },
];

const viewFromPath = (pathname: string): DashboardView => {
  if (pathname.endsWith("/account")) return "account";
  if (pathname.endsWith("/saved-events")) return "saved-events";
  if (pathname.endsWith("/notifications")) return "notifications";
  if (pathname.endsWith("/password") || pathname.endsWith("/security")) return "password";
  if (pathname.endsWith("/support")) return "support";
  if (pathname.endsWith("/remove-account")) return "remove-account";

  return "overview";
};

const viewCopy: Record<DashboardView, { eyebrow: string; title: string; description: string }> = {
  overview: {
    eyebrow: "Member",
    title: "Dashboard",
    description: "Manage your Black Sky account, saved events, notifications, and account security.",
  },
  account: {
    eyebrow: "Account",
    title: "Account details",
    description: "Keep your contact details ready for ticket sync, event alerts, and support.",
  },
  "saved-events": {
    eyebrow: "Saved events",
    title: "Saved events",
    description: "Concerts and shows you saved from Black Sky pages.",
  },
  notifications: {
    eyebrow: "Notifications",
    title: "Event alerts",
    description: "Black Sky announcements, ticket updates, and account messages.",
  },
  password: {
    eyebrow: "Password",
    title: "Change Password",
    description: "Change your password without leaving your member dashboard.",
  },
  support: {
    eyebrow: "Support",
    title: "Support",
    description: "Report ticket, account, or event access issues to the Black Sky team.",
  },
  "remove-account": {
    eyebrow: "Security",
    title: "Remove Account",
    description: "Permanently remove your member profile, saved events, and account access.",
  },
};

const menuGroups: Array<{ title: string; items: MenuItem[] }> = [
  {
    title: "Account",
    items: [
      {
        view: "account",
        href: "/dashboard/account",
        label: "Account",
        description: "View and edit your account",
        Icon: UserRound,
      },
      {
        view: "saved-events",
        href: "/dashboard/saved-events",
        label: "Saved Events",
        description: "Your favorite concerts and shows",
        Icon: Heart,
      },
      {
        view: "notifications",
        href: "/dashboard/notifications",
        label: "Notifications",
        description: "Alerts and announcements",
        Icon: Bell,
      },
      {
        view: "support",
        href: "/dashboard/support",
        label: "Support",
        description: "Report ticket or account issues",
        Icon: LifeBuoy,
      },
    ],
  },
  {
    title: "Security",
    items: [
      {
        view: "password",
        href: "/dashboard/password",
        label: "Password",
        description: "Change your password",
        Icon: ShieldCheck,
      },
      {
        view: "remove-account",
        href: "/dashboard/remove-account",
        label: "Remove Account",
        description: "Permanently erase your data and account",
        Icon: Trash2,
      },
    ],
  },
];

function formatDate(value?: string | null) {
  if (!value) return "Soon";

  return new Intl.DateTimeFormat("en-MY", {
    day: "2-digit",
    month: "short",
    year: "numeric",
  }).format(new Date(value));
}

function emptyBirthDateParts(): BirthDateParts {
  return {
    day: "",
    month: "",
    year: "",
  };
}

function parseBirthDate(value?: string | null): BirthDateParts {
  const match = /^(\d{4})-(\d{2})-(\d{2})$/.exec(value ?? "");

  if (!match) return emptyBirthDateParts();

  return {
    year: match[1],
    month: match[2],
    day: match[3],
  };
}

function daysInMonth(year: string, month: string) {
  const numericYear = Number(year || currentYear);
  const numericMonth = Number(month || 1);

  return new Date(numericYear, numericMonth, 0).getDate();
}

function allowedBirthDayCount(year: string, month: string) {
  if (!year || !month) return 31;

  const maxDays = daysInMonth(year, month);

  if (Number(year) === currentYear && Number(month) === currentMonth) {
    return Math.min(maxDays, currentDay);
  }

  return maxDays;
}

function birthDateFromParts(parts: BirthDateParts) {
  if (!parts.year || !parts.month || !parts.day) return "";

  return `${parts.year}-${parts.month}-${parts.day}`;
}

function normalizeBirthDateParts(parts: BirthDateParts): BirthDateParts {
  const next = { ...parts };

  if (Number(next.year) === currentYear && Number(next.month) > currentMonth) {
    next.month = "";
    next.day = "";
  }

  if (next.year && next.month && next.day) {
    const maxDay = allowedBirthDayCount(next.year, next.month);

    if (Number(next.day) > maxDay) {
      next.day = String(maxDay).padStart(2, "0");
    }
  }

  return next;
}

function userInitials(name?: string | null, email?: string | null) {
  const source = name?.trim() || email?.split("@").at(0) || "BS";
  const parts = source.split(/\s+/).filter(Boolean);

  return parts
    .slice(0, 2)
    .map((part) => part[0]?.toUpperCase())
    .join("");
}

function normalizeGender(value?: string | null): AccountFormValues["gender"] {
  return value === "male" ||
    value === "female" ||
    value === "non_binary" ||
    value === "prefer_not_to_say"
    ? value
    : "";
}

function amountLabel(ticket: MemberTicket) {
  if (!ticket.total_amount) return ticket.currency;

  return `${ticket.currency} ${ticket.total_amount}`;
}

function DashboardMenu({ activeView }: { activeView: DashboardView }) {
  return (
    <div className="member-menu-list">
      {menuGroups.map((group) => (
        <section key={group.title} className="member-menu-group">
          <h2>{group.title}</h2>
          <div>
            {group.items.map(({ href, label, description, Icon, view }) => (
              <Link
                key={href}
                className="member-menu-row"
                data-active={view === activeView}
                to={href}
              >
                <Icon aria-hidden="true" />
                <span>
                  <strong>{label}</strong>
                  <small>{description}</small>
                </span>
                <ChevronRight aria-hidden="true" />
              </Link>
            ))}
          </div>
        </section>
      ))}
    </div>
  );
}

function ProfileSummary({
  name,
  email,
  avatar,
  onSignOut,
  isSigningOut,
}: {
  name?: string | null;
  email?: string | null;
  avatar?: string | null;
  onSignOut: () => void;
  isSigningOut: boolean;
}) {
  return (
    <section className="member-profile-summary" aria-label="Account summary">
      <span>Account</span>
      <div className="member-profile-summary__body">
        <div className="member-profile-summary__avatar">
          {avatar ? (
            <img src={avatar} alt="" />
          ) : (
            <strong>{userInitials(name, email)}</strong>
          )}
        </div>
        <div>
          <h2>{name ?? "Black Sky Member"}</h2>
          <p>{email ?? "Loading account"}</p>
        </div>
        <button type="button" disabled={isSigningOut} onClick={onSignOut}>
          <LogOut aria-hidden="true" />
          Sign out
        </button>
      </div>
    </section>
  );
}

function EmptyState({
  icon: Icon,
  title,
  description,
  action,
}: {
  icon: typeof UserRound;
  title: string;
  description: string;
  action?: { label: string; href: string };
}) {
  return (
    <div className="member-empty">
      <div>
        <Icon aria-hidden="true" />
      </div>
      <strong>{title}</strong>
      <p>{description}</p>
      {action ? (
        <Link to={action.href}>
          {action.label}
          <ArrowRight aria-hidden="true" />
        </Link>
      ) : null}
    </div>
  );
}

function DateOfBirthDropdown({
  value,
  onChange,
  onBlur,
}: {
  value?: string;
  onChange: (value: string) => void;
  onBlur?: () => void;
}) {
  const [parts, setParts] = useState<BirthDateParts>(() => parseBirthDate(value));
  const dayOptions = useMemo(
    () =>
      Array.from({ length: allowedBirthDayCount(parts.year, parts.month) }, (_, index) =>
        String(index + 1).padStart(2, "0"),
      ),
    [parts.month, parts.year],
  );

  useEffect(() => {
    setParts(parseBirthDate(value));
  }, [value]);

  const setPart = (key: keyof BirthDateParts, nextValue: string) => {
    setParts((current) => {
      const next = normalizeBirthDateParts({
        ...current,
        [key]: nextValue,
      });

      onChange(birthDateFromParts(next));

      return next;
    });
  };

  return (
    <div
      className="member-birthdate-selects"
      role="group"
      aria-labelledby="member-date-of-birth-label"
    >
      <select
        id="member-birth-day"
        aria-label="Birth day"
        value={parts.day}
        onBlur={onBlur}
        onChange={(event) => setPart("day", event.target.value)}
      >
        <option value="">Day</option>
        {dayOptions.map((day) => (
          <option key={day} value={day}>
            {day}
          </option>
        ))}
      </select>
      <select
        id="member-birth-month"
        aria-label="Birth month"
        value={parts.month}
        onBlur={onBlur}
        onChange={(event) => setPart("month", event.target.value)}
      >
        <option value="">Month</option>
        {birthMonthOptions.map((month) => (
          <option
            key={month.value}
            value={month.value}
            disabled={Number(parts.year) === currentYear && Number(month.value) > currentMonth}
          >
            {month.label}
          </option>
        ))}
      </select>
      <select
        id="member-birth-year"
        aria-label="Birth year"
        value={parts.year}
        onBlur={onBlur}
        onChange={(event) => setPart("year", event.target.value)}
      >
        <option value="">Year</option>
        {birthYearOptions.map((year) => (
          <option key={year} value={year}>
            {year}
          </option>
        ))}
      </select>
    </div>
  );
}

function TicketRow({ ticket }: { ticket: MemberTicket }) {
  return (
    <article className="member-ticket-row">
      <div className="member-ticket-row__icon">
        <Ticket aria-hidden="true" />
      </div>
      <div className="member-ticket-row__main">
        <span>{ticket.status}</span>
        <h3>{ticket.event_title ?? ticket.event?.title ?? "Black Sky event"}</h3>
        <p>
          {ticket.ticket_type ?? "General admission"} · {ticket.quantity} ticket
          {ticket.quantity > 1 ? "s" : ""}
        </p>
      </div>
      <div className="member-ticket-row__meta">
        <strong>{amountLabel(ticket)}</strong>
        <small>{formatDate(ticket.purchased_at)}</small>
      </div>
    </article>
  );
}

function NotificationRow({ notification }: { notification: MemberNotification }) {
  return (
    <article className="member-notification-row" data-unread={!notification.read_at}>
      <Bell aria-hidden="true" />
      <div>
        <strong>{notification.title}</strong>
        <p>{notification.body}</p>
      </div>
      <small>{formatDate(notification.created_at)}</small>
    </article>
  );
}

function TicketGroup({
  title,
  tickets,
  empty,
}: {
  title: string;
  tickets: MemberTicket[];
  empty: string;
}) {
  return (
    <section className="member-ticket-section">
      <div className="member-ticket-section__head">
        <h3>{title}</h3>
        <span>{tickets.length}</span>
      </div>
      {tickets.length ? (
        <div className="member-list">
          {tickets.map((ticket) => (
            <TicketRow key={ticket.id} ticket={ticket} />
          ))}
        </div>
      ) : (
        <div className="member-ticket-empty">{empty}</div>
      )}
    </section>
  );
}

export function UserDashboardPage() {
  const location = useLocation();
  const navigate = useNavigate();
  const activeView = viewFromPath(location.pathname);
  const copy = viewCopy[activeView];
  const dashboardQuery = useMemberDashboard();
  const logoutMutation = useLogoutMutation();
  const updateAccountMutation = useUpdateMemberAccount();
  const updatePasswordMutation = useUpdateMemberPassword();
  const deleteAccountMutation = useDeleteMemberAccount();
  const [accountMessage, setAccountMessage] = useState("");
  const [accountError, setAccountError] = useState("");
  const [passwordMessage, setPasswordMessage] = useState("");
  const [passwordError, setPasswordError] = useState("");
  const [removeAccountMessage, setRemoveAccountMessage] = useState("");
  const [removeAccountError, setRemoveAccountError] = useState("");
  const [avatarFile, setAvatarFile] = useState<File | null>(null);
  const [avatarPreview, setAvatarPreview] = useState<string | null>(null);
  const avatarInputRef = useRef<HTMLInputElement | null>(null);
  const data = dashboardQuery.data;
  const user = data?.user;

  const accountForm = useForm<AccountFormValues>({
    resolver: zodResolver(accountSchema),
    defaultValues: {
      fullName: "",
      email: "",
      phone: "",
      countryCode: "",
      dateOfBirth: "",
      gender: "",
    },
  });
  const passwordForm = useForm<PasswordFormValues>({
    resolver: zodResolver(passwordSchema),
    defaultValues: {
      currentPassword: "",
      password: "",
      passwordConfirmation: "",
    },
  });
  const removeAccountForm = useForm<RemoveAccountFormValues>({
    resolver: zodResolver(removeAccountSchema),
    defaultValues: {
      password: "",
    },
  });

  const today = new Date().toISOString().slice(0, 10);
  const upcomingSavedCount = useMemo(
    () =>
      (data?.saved_events ?? []).filter(
        (saved) => saved.event.start_date && saved.event.start_date >= today,
      ).length,
    [data?.saved_events, today],
  );

  useEffect(() => {
    if (!user) return;

    accountForm.reset({
      fullName: user.name ?? "",
      email: user.email,
      phone: user.phone ?? "",
      countryCode: user.countryCode ?? "",
      dateOfBirth: user.dateOfBirth ?? "",
      gender: normalizeGender(user.gender),
    });
    setAvatarFile(null);
    setAvatarPreview(user.avatar ?? null);
  }, [accountForm, user]);

  useEffect(() => {
    return () => {
      if (avatarPreview?.startsWith("blob:")) {
        URL.revokeObjectURL(avatarPreview);
      }
    };
  }, [avatarPreview]);

  const signOut = async () => {
    await logoutMutation.mutateAsync();
    navigate("/login", { replace: true });
  };

  const handleAvatarChange = (file?: File | null) => {
    if (!file) return;

    setAccountError("");
    setAvatarFile(file);
    setAvatarPreview((current) => {
      if (current?.startsWith("blob:")) {
        URL.revokeObjectURL(current);
      }

      return URL.createObjectURL(file);
    });
  };

  const submitAccount = accountForm.handleSubmit(async (values) => {
    setAccountMessage("");
    setAccountError("");

    try {
      await updateAccountMutation.mutateAsync({
        name: values.fullName.trim(),
        email: values.email,
        phone: values.phone ?? "",
        country_code: values.countryCode ?? "",
        date_of_birth: values.dateOfBirth ?? "",
        gender: values.gender || "",
        avatar: avatarFile,
      });
      setAvatarFile(null);
      setAccountMessage("Account details updated.");
    } catch (error) {
      setAccountError(getAuthErrorMessage(error, "Unable to update account."));
    }
  });
  const submitPassword = passwordForm.handleSubmit(async (values) => {
    setPasswordMessage("");
    setPasswordError("");

    try {
      await updatePasswordMutation.mutateAsync({
        current_password: values.currentPassword,
        password: values.password,
        password_confirmation: values.passwordConfirmation,
      });
      passwordForm.reset();
      setPasswordMessage("Password updated.");
    } catch (error) {
      setPasswordError(getAuthErrorMessage(error, "Unable to update password."));
    }
  });
  const submitRemoveAccount = removeAccountForm.handleSubmit(async (values) => {
    setRemoveAccountMessage("");
    setRemoveAccountError("");

    try {
      await deleteAccountMutation.mutateAsync({
        password: values.password,
      });
      setRemoveAccountMessage("Account removed.");
      navigate("/login", { replace: true });
    } catch (error) {
      setRemoveAccountError(getAuthErrorMessage(error, "Unable to remove account."));
    }
  });

  return (
    <>
      <Navbar />
      <main className="member-dashboard" aria-label="Black Sky member dashboard">
        <section className="member-dashboard__intro">
          <span>Hi, {user?.name?.split(" ").at(0) ?? "Member"}</span>
          <h1>{copy.title}</h1>
          <p>{copy.description}</p>
        </section>

        {dashboardQuery.isLoading ? (
          <div className="member-loading">Loading your member area...</div>
        ) : null}

        {!dashboardQuery.isLoading && dashboardQuery.isError ? (
          <EmptyState
            icon={ShieldCheck}
            title="Dashboard unavailable"
            description="We could not load your member data. Please refresh or sign in again."
            action={{ label: "Back to login", href: "/login" }}
          />
        ) : null}

        {!dashboardQuery.isLoading && data ? (
          <div className="member-dashboard__content">
            {activeView === "overview" ? (
              <>
                <ProfileSummary
                  name={user?.name}
                  email={user?.email}
                  avatar={user?.avatar}
                  isSigningOut={logoutMutation.isPending}
                  onSignOut={signOut}
                />
                <DashboardMenu activeView={activeView} />
              </>
            ) : (
              <section className="member-detail-view">
                <Link className="member-back-link" to="/dashboard">
                  <ArrowLeft aria-hidden="true" />
                  Dashboard
                </Link>
                <div className="member-detail-view__head">
                  <span>{copy.eyebrow}</span>
                  <h2>{copy.title}</h2>
                </div>

                {activeView === "account" ? (
                  <form className="member-account-form member-account-form--profile" onSubmit={submitAccount} noValidate>
                    <section className="member-account-card" aria-label="Member account">
                      <span>Account</span>
                      <div className="member-account-card__identity">
                        <div className="member-avatar-upload">
                          <div className="member-avatar-upload__preview">
                            {avatarPreview ? (
                              <img src={avatarPreview} alt="" />
                            ) : (
                              <strong>{userInitials(user?.name, user?.email)}</strong>
                            )}
                          </div>
                          <input
                            ref={avatarInputRef}
                            id="member-avatar"
                            type="file"
                            accept="image/png,image/jpeg,image/webp,image/gif"
                            onChange={(event) => handleAvatarChange(event.target.files?.[0])}
                          />
                          <button
                            type="button"
                            className="member-avatar-upload__button"
                            onClick={() => avatarInputRef.current?.click()}
                          >
                            <Camera aria-hidden="true" />
                            Change photo
                          </button>
                        </div>
                        <div>
                          <strong>{user?.name ?? "Black Sky Member"}</strong>
                          <p>{user?.email}</p>
                          <small>
                            <Upload aria-hidden="true" />
                            JPG, PNG, WEBP or GIF
                          </small>
                        </div>
                      </div>
                    </section>
                    <div className="member-form-grid">
                      <div className="member-field member-field--full">
                        <Label htmlFor="member-full-name">
                          <span>*</span>
                          Full name
                        </Label>
                        <Input id="member-full-name" {...accountForm.register("fullName")} />
                        {accountForm.formState.errors.fullName ? (
                          <p>{accountForm.formState.errors.fullName.message}</p>
                        ) : null}
                      </div>
                      <div className="member-field">
                        <Label id="member-date-of-birth-label">Date of birth</Label>
                        <Controller
                          control={accountForm.control}
                          name="dateOfBirth"
                          render={({ field }) => (
                            <DateOfBirthDropdown
                              value={field.value ?? ""}
                              onChange={field.onChange}
                              onBlur={field.onBlur}
                            />
                          )}
                        />
                      </div>
                      <div className="member-field">
                        <Label htmlFor="member-gender">Gender</Label>
                        <select id="member-gender" {...accountForm.register("gender")}>
                          <option value="">Select gender</option>
                          <option value="male">Male</option>
                          <option value="female">Female</option>
                          <option value="non_binary">Non-binary</option>
                          <option value="prefer_not_to_say">Prefer not to say</option>
                        </select>
                      </div>
                      <div className="member-field member-field--full">
                        <Label htmlFor="member-email">Email address</Label>
                        <Input
                          id="member-email"
                          type="email"
                          readOnly
                          aria-readonly="true"
                          {...accountForm.register("email")}
                        />
                        {accountForm.formState.errors.email ? (
                          <p>{accountForm.formState.errors.email.message}</p>
                        ) : null}
                      </div>
                      <div className="member-field">
                        <Label htmlFor="member-country">Country</Label>
                        <Controller
                          control={accountForm.control}
                          name="countryCode"
                          render={({ field }) => (
                            <RegistrationCountryDropdown
                              id="member-country"
                              value={field.value ?? ""}
                              onChange={field.onChange}
                            />
                          )}
                        />
                      </div>
                      <div className="member-field">
                        <Label htmlFor="member-phone">Mobile number</Label>
                        <Controller
                          control={accountForm.control}
                          name="phone"
                          render={({ field }) => (
                            <PhoneInput
                              id="member-phone"
                              value={field.value ?? ""}
                              defaultCountry={accountForm.watch("countryCode") || "MY"}
                              onChange={field.onChange}
                              onBlur={field.onBlur}
                              placeholder="Enter phone number"
                            />
                          )}
                        />
                      </div>
                    </div>
                    <p
                      className={accountError ? "member-form-message is-error" : "member-form-message"}
                      role={accountError ? "alert" : "status"}
                    >
                      {accountError || accountMessage}
                    </p>
                    <div className="member-form-actions">
                      <button type="submit" disabled={updateAccountMutation.isPending}>
                        {updateAccountMutation.isPending ? "Saving" : "Save changes"}
                      </button>
                      <button
                        type="button"
                        className="member-secondary-button"
                        onClick={() => {
                          accountForm.reset({
                            fullName: user?.name ?? "",
                            email: user?.email ?? "",
                            phone: user?.phone ?? "",
                            countryCode: user?.countryCode ?? "",
                            dateOfBirth: user?.dateOfBirth ?? "",
                            gender: normalizeGender(user?.gender),
                          });
                          setAvatarFile(null);
                          setAvatarPreview(user?.avatar ?? null);
                          setAccountMessage("");
                          setAccountError("");
                        }}
                      >
                        Cancel
                      </button>
                    </div>
                  </form>
                ) : null}

                {activeView === "saved-events" ? (
                  <div className="member-saved-view">
                    <div className="member-wishlist-summary">
                      <span>Wishlist</span>
                      <div>
                        <strong>Total saved events</strong>
                        <b>{data.stats.saved_events}</b>
                      </div>
                      <div>
                        <strong>Upcoming saved events</strong>
                        <b>{upcomingSavedCount}</b>
                      </div>
                    </div>
                    {data.saved_events.length ? (
                      <div className="member-event-grid member-event-grid--landing">
                        {data.saved_events.map((saved) => (
                          <EventSlideCard
                            key={saved.id}
                            event={mapPublicEventToSlide(saved.event)}
                            isActive
                            fluid
                          />
                        ))}
                      </div>
                    ) : (
                      <EmptyState
                        icon={Heart}
                        title="No saved events yet"
                        description="Start exploring and click the save button to keep events here."
                        action={{ label: "Discover shows", href: "/discover" }}
                      />
                    )}
                  </div>
                ) : null}

                {activeView === "notifications" ? (
                  data.notifications.length ? (
                    <div className="member-list">
                      {data.notifications.map((notification) => (
                        <NotificationRow key={notification.id} notification={notification} />
                      ))}
                    </div>
                  ) : (
                    <EmptyState
                      icon={Bell}
                      title="No notifications yet"
                      description="Event announcements and ticket updates from Black Sky will appear here."
                    />
                  )
                ) : null}

                {activeView === "support" ? (
                  <div className="member-support-grid">
                    <article>
                      <LifeBuoy aria-hidden="true" />
                      <span>Customer service</span>
                      <h3>Need help with an event or ticket?</h3>
                      <p>
                        Send the Black Sky team your account email, event name, and vendor order number so the issue can be traced quickly.
                      </p>
                      <a href="mailto:hello@blackskyenterprise.com?subject=Black%20Sky%20Member%20Support">
                        Email support
                        <ArrowRight aria-hidden="true" />
                      </a>
                    </article>
                  </div>
                ) : null}

                {activeView === "password" ? (
                  <form className="member-account-form member-password-form" onSubmit={submitPassword} noValidate>
                    <div className="member-form-grid">
                      <div className="member-field">
                        <Label htmlFor="member-current-password">Current password*</Label>
                        <Input
                          id="member-current-password"
                          type="password"
                          autoComplete="current-password"
                          {...passwordForm.register("currentPassword")}
                        />
                        {passwordForm.formState.errors.currentPassword ? (
                          <p>{passwordForm.formState.errors.currentPassword.message}</p>
                        ) : null}
                      </div>
                      <div className="member-field">
                        <Label htmlFor="member-new-password">New password*</Label>
                        <Input
                          id="member-new-password"
                          type="password"
                          autoComplete="new-password"
                          {...passwordForm.register("password")}
                        />
                        {passwordForm.formState.errors.password ? (
                          <p>{passwordForm.formState.errors.password.message}</p>
                        ) : null}
                      </div>
                      <div className="member-field">
                        <Label htmlFor="member-password-confirmation">Confirm new password*</Label>
                        <Input
                          id="member-password-confirmation"
                          type="password"
                          autoComplete="new-password"
                          {...passwordForm.register("passwordConfirmation")}
                        />
                        {passwordForm.formState.errors.passwordConfirmation ? (
                          <p>{passwordForm.formState.errors.passwordConfirmation.message}</p>
                        ) : null}
                      </div>
                    </div>
                    <p
                      className={passwordError ? "member-form-message is-error" : "member-form-message"}
                      role={passwordError ? "alert" : "status"}
                    >
                      {passwordError || passwordMessage}
                    </p>
                    <button type="submit" disabled={updatePasswordMutation.isPending}>
                      {updatePasswordMutation.isPending ? "Saving" : "Save changes"}
                    </button>
                  </form>
                ) : null}

                {activeView === "remove-account" ? (
                  <form className="member-account-form member-remove-account-form" onSubmit={submitRemoveAccount} noValidate>
                    <div className="member-danger-panel">
                      <Trash2 aria-hidden="true" />
                      <span>Permanent action</span>
                      <h3>Remove your Black Sky account</h3>
                      <p>
                        This removes your member profile and saved events. Synced ticket records keep their vendor audit trail, but they will no longer be attached to this login.
                      </p>
                    </div>
                    <div className="member-form-grid">
                      <div className="member-field">
                        <Label htmlFor="member-remove-password">
                          <span>*</span>
                          Confirm password
                        </Label>
                        <Input
                          id="member-remove-password"
                          type="password"
                          autoComplete="current-password"
                          {...removeAccountForm.register("password")}
                        />
                        {removeAccountForm.formState.errors.password ? (
                          <p>{removeAccountForm.formState.errors.password.message}</p>
                        ) : null}
                      </div>
                    </div>
                    <p
                      className={removeAccountError ? "member-form-message is-error" : "member-form-message"}
                      role={removeAccountError ? "alert" : "status"}
                    >
                      {removeAccountError || removeAccountMessage}
                    </p>
                    <div className="member-form-actions">
                      <button
                        type="submit"
                        className="member-danger-button"
                        disabled={deleteAccountMutation.isPending}
                      >
                        {deleteAccountMutation.isPending ? "Removing" : "Remove account"}
                      </button>
                      <Link className="member-secondary-button" to="/dashboard">
                        Cancel
                      </Link>
                    </div>
                  </form>
                ) : null}
              </section>
            )}
          </div>
        ) : null}
      </main>
      <Footer />
    </>
  );
}
