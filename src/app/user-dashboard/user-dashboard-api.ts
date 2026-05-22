import { http } from "../lib/http";
import { toAuthUser, type AuthUser } from "../auth/auth-api";
import type { PublicEvent } from "../events/events-api";

export type MemberTicket = {
  id: number;
  vendor: string;
  external_order_id: string | null;
  event_title: string | null;
  ticket_type: string | null;
  quantity: number;
  total_amount: string | null;
  currency: string;
  status: string;
  purchased_at: string | null;
  event: PublicEvent | null;
};

export type SavedEvent = {
  id: number;
  created_at: string | null;
  event: PublicEvent;
};

export type MemberNotification = {
  id: string;
  title: string;
  body: string;
  type: string;
  source?: "admin" | "activity" | string;
  action_url?: string | null;
  read_at: string | null;
  created_at: string | null;
};

export type MemberDashboardStats = {
  tickets: number;
  saved_events: number;
  unread_notifications: number;
  upcoming_events: number;
};

export type MemberDashboardPayload = {
  user: AuthUser;
  stats: MemberDashboardStats;
  tickets: MemberTicket[];
  saved_events: SavedEvent[];
  notifications: MemberNotification[];
  upcoming_events: PublicEvent[];
};

export type UpdateAccountPayload = {
  name: string;
  email: string;
  phone?: string | null;
  country_code?: string | null;
  date_of_birth?: string | null;
  gender?: string | null;
  avatar?: File | null;
};

export type UpdatePasswordPayload = {
  current_password: string;
  password: string;
  password_confirmation: string;
};

export type DeleteAccountPayload = {
  password: string;
};

export async function getMemberDashboard() {
  const { data } = await http.get<{ data: MemberDashboardPayload }>(
    "/api/v1/me/dashboard",
  );

  return {
    ...data.data,
    user: toAuthUser(data.data.user),
  };
}

export async function updateMemberAccount(payload: UpdateAccountPayload) {
  await http.get("/sanctum/csrf-cookie");

  const formData = new FormData();
  formData.set("name", payload.name);
  formData.set("email", payload.email);
  formData.set("phone", payload.phone ?? "");
  formData.set("country_code", payload.country_code ?? "");
  formData.set("date_of_birth", payload.date_of_birth ?? "");
  formData.set("gender", payload.gender ?? "");

  if (payload.avatar) {
    formData.set("avatar", payload.avatar);
  }

  const { data } = await http.post<{ data: AuthUser; message: string }>(
    "/api/v1/me/account",
    formData,
  );

  return {
    ...data,
    data: toAuthUser(data.data),
  };
}

export async function updateMemberPassword(payload: UpdatePasswordPayload) {
  await http.get("/sanctum/csrf-cookie");

  const { data } = await http.patch<{ message: string }>(
    "/api/v1/me/password",
    payload,
  );

  return data;
}

export async function deleteMemberAccount(payload: DeleteAccountPayload) {
  await http.get("/sanctum/csrf-cookie");

  const { data } = await http.delete<{ message: string }>("/api/v1/me/account", {
    data: payload,
  });

  return data;
}

export async function saveEvent(eventId: number) {
  await http.get("/sanctum/csrf-cookie");

  const { data } = await http.post(`/api/v1/me/bookmarks/${eventId}`);

  return data;
}

export async function removeSavedEvent(eventId: number) {
  await http.get("/sanctum/csrf-cookie");

  const { data } = await http.delete(`/api/v1/me/bookmarks/${eventId}`);

  return data;
}
