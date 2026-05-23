import DOMPurify from "dompurify";

const allowedTags = [
  "p",
  "br",
  "strong",
  "b",
  "em",
  "i",
  "u",
  "h2",
  "h3",
  "h4",
  "ul",
  "ol",
  "li",
  "blockquote",
  "hr",
  "pre",
  "code",
  "a",
];

const allowedAttributes = ["href", "title", "target", "rel"];

export function sanitizeRichHtml(html: string) {
  return DOMPurify.sanitize(html, {
    ALLOWED_TAGS: allowedTags,
    ALLOWED_ATTR: allowedAttributes,
    FORBID_TAGS: ["script", "style", "iframe", "object", "embed", "form", "input", "button", "svg", "math"],
    FORBID_ATTR: ["style"],
    ALLOW_DATA_ATTR: false,
  });
}
