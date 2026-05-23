import { http } from "../lib/http";

export type BlogTaxonomy = {
  name: string;
  slug: string;
};

export type BlogAuthor = {
  name: string;
  slug: string;
  bio: string | null;
  photo: string | null;
};

export type BlogPostSummary = {
  id: number;
  title: string;
  slug: string;
  excerpt: string | null;
  featured_image: string | null;
  published_at: string | null;
  updated_at: string | null;
  reading_minutes: number;
  view_count: number;
  author: BlogAuthor | null;
  category: BlogTaxonomy | null;
  tags: BlogTaxonomy[];
};

export type BlogPostDetail = BlogPostSummary & {
  content: string;
  meta_title: string | null;
  meta_description: string | null;
  meta_keywords: string | null;
  canonical_url: string | null;
  og_image: string | null;
};

export type BlogPostsResponse = {
  data: BlogPostSummary[];
  meta: {
    per_page: number;
    next_cursor: string | null;
    filters?: {
      categories: BlogTaxonomy[];
      tags: BlogTaxonomy[];
    };
  };
};

export type BlogPostQuery = {
  search?: string;
  category?: string;
  tag?: string;
  cursor?: string;
  perPage?: number;
  sort?: "published" | "latest";
};

export async function getBlogPosts(query: BlogPostQuery = {}) {
  const params = new URLSearchParams();

  if (query.search) params.set("search", query.search);
  if (query.category) params.set("category", query.category);
  if (query.tag) params.set("tag", query.tag);
  if (query.cursor) params.set("cursor", query.cursor);
  if (query.sort) params.set("sort", query.sort);
  params.set("per_page", String(query.perPage ?? 9));

  const { data } = await http.get<BlogPostsResponse>(`/api/v1/news?${params}`);

  return data;
}

export async function getBlogPost(slug: string) {
  const { data } = await http.get<{ data: BlogPostDetail }>(`/api/v1/news/${slug}`);

  return data.data;
}
