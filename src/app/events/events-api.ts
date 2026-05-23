import { http } from "../lib/http";

export type PublicEvent = {
  id: number;
  title: string;
  slug: string;
  subtitle: string | null;
  venue: string;
  city: string;
  country_code: string;
  genre: string;
  date: string;
  start_date: string;
  start_time: string | null;
  time: string | null;
  end_date: string | null;
  status: "published" | "sold_out" | string;
  image_url: string | null;
  accent_color: string | null;
  glow_color: string | null;
  vendor_url: string | null;
};

export type PublicEventSection = {
  id: number;
  section_key: string;
  title: string;
  content: string | null;
  image_url: string | null;
};

export type PublicEventDetail = PublicEvent & {
  timezone: string;
  organizer_name: string;
  organizer_url: string | null;
  spotify_embed_url: string | null;
  sections: PublicEventSection[];
  meta_title: string | null;
  meta_description: string | null;
  meta_keywords: string | null;
  canonical_url: string | null;
  og_image: string | null;
};

export type PublicEventFilters = {
  cities: string[];
  genres: string[];
};

export type PublicEventsResponse = {
  data: PublicEvent[];
  meta: {
    per_page: number;
    next_cursor: string | null;
    filters?: PublicEventFilters;
  };
};

export type PublicEventQuery = {
  search?: string;
  city?: string;
  genre?: string;
  dateFrom?: string;
  dateTo?: string;
  timeframe?: "upcoming" | "past";
  availability?: "all" | "available" | "sold_out";
  sort?: "date" | "latest";
  cursor?: string;
  perPage?: number;
};

export async function getPublicEvents(query: PublicEventQuery = {}) {
  const params = new URLSearchParams();

  if (query.search) params.set("search", query.search);
  if (query.city) params.set("city", query.city);
  if (query.genre) params.set("genre", query.genre);
  if (query.dateFrom) params.set("date_from", query.dateFrom);
  if (query.dateTo) params.set("date_to", query.dateTo);
  if (query.timeframe) params.set("timeframe", query.timeframe);
  if (query.availability && query.availability !== "all") {
    params.set("availability", query.availability);
  }
  if (query.sort && query.sort !== "date") params.set("sort", query.sort);
  if (query.cursor) params.set("cursor", query.cursor);
  params.set("per_page", String(query.perPage ?? 10));

  const { data } = await http.get<PublicEventsResponse>(`/api/v1/events?${params}`);

  return data;
}

export async function getPublicEvent(slug: string) {
  const { data } = await http.get<{ data: PublicEventDetail }>(
    `/api/v1/events/${encodeURIComponent(slug)}`,
  );

  return data.data;
}
