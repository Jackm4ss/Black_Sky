import { type PointerEvent, useMemo } from "react";
import { Heart } from "lucide-react";
import { useLocation, useNavigate } from "react-router";
import { isAuthUserEmailVerified } from "../auth/auth-api";
import { useCurrentUser } from "../auth/auth-queries";
import {
  useMemberDashboard,
  useRemoveSavedEventMutation,
  useSaveEventMutation,
} from "../user-dashboard/user-dashboard-queries";
import "./SaveEventButton.css";

type SaveEventButtonProps = {
  eventId: number;
  className?: string;
  idleLabel?: string;
  savedLabel?: string;
  loginLabel?: string;
  compact?: boolean;
};

export function SaveEventButton({
  eventId,
  className = "",
  idleLabel = "Save Event",
  savedLabel = "Saved",
  loginLabel = "Save Event",
  compact = false,
}: SaveEventButtonProps) {
  const navigate = useNavigate();
  const location = useLocation();
  const { data: user, isLoading: isUserLoading } = useCurrentUser();
  const isMember = Boolean(user?.roles?.includes("user"));
  const isVerifiedMember = Boolean(isMember && isAuthUserEmailVerified(user));
  const isAdminOnly = Boolean(user?.roles?.includes("admin") && !isMember);
  const dashboardQuery = useMemberDashboard({ enabled: isVerifiedMember });
  const saveMutation = useSaveEventMutation();
  const removeMutation = useRemoveSavedEventMutation();
  const savedEventIds = useMemo(
    () => new Set(dashboardQuery.data?.saved_events.map((saved) => saved.event.id) ?? []),
    [dashboardQuery.data?.saved_events],
  );
  const isSaved = savedEventIds.has(eventId);
  const isBusy =
    isUserLoading ||
    (isVerifiedMember && dashboardQuery.isLoading) ||
    saveMutation.isPending ||
    removeMutation.isPending;
  const label = isAdminOnly
    ? "Members Only"
    : isSaved
      ? savedLabel
      : user
        ? isVerifiedMember
          ? idleLabel
          : "Verify Email"
        : loginLabel;

  const handlePointerDown = (event: PointerEvent<HTMLButtonElement>) => {
    event.stopPropagation();
  };

  const handleClick = () => {
    if (isBusy || isAdminOnly) {
      return;
    }

    if (!user) {
      navigate("/login", {
        state: {
          from: `${location.pathname}${location.search}${location.hash}`,
        },
      });
      return;
    }

    if (isMember && !isVerifiedMember) {
      navigate("/email-verification", {
        state: {
          from: `${location.pathname}${location.search}${location.hash}`,
        },
      });
      return;
    }

    if (isSaved) {
      removeMutation.mutate(eventId);
      return;
    }

    saveMutation.mutate(eventId);
  };

  return (
    <button
      type="button"
      className={`save-event-button ${compact ? "save-event-button--compact" : ""} ${className}`.trim()}
      data-saved={isSaved}
      aria-pressed={isSaved}
      disabled={isBusy || isAdminOnly}
      onPointerDown={handlePointerDown}
      onClick={handleClick}
    >
      <Heart aria-hidden="true" />
      <span>{isBusy ? "Checking" : label}</span>
    </button>
  );
}
