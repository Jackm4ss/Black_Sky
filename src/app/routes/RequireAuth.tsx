import type { ReactNode } from "react";
import { Navigate, useLocation } from "react-router";
import { isAuthUserEmailVerified } from "../auth/auth-api";
import { useCurrentUser } from "../auth/auth-queries";

type RequireAuthProps = {
  children: ReactNode;
  allowedRoles?: string[];
  forbiddenRedirectTo?: string;
  requireVerified?: boolean;
};

export function RequireAuth({
  children,
  allowedRoles,
  forbiddenRedirectTo = "/",
  requireVerified = false,
}: RequireAuthProps) {
  const location = useLocation();
  const { data: user, isLoading } = useCurrentUser();

  if (isLoading) {
    return null;
  }

  if (!user) {
    return <Navigate to="/login" replace state={{ from: location.pathname }} />;
  }

  if (allowedRoles?.length) {
    const hasAllowedRole = user.roles?.some((role) => allowedRoles.includes(role));

    if (!hasAllowedRole) {
      const redirectTo = user.roles?.includes("admin") ? "/admin" : forbiddenRedirectTo;

      return <Navigate to={redirectTo} replace />;
    }
  }

  if (requireVerified && !isAuthUserEmailVerified(user)) {
    return <Navigate to="/email-verification" replace state={{ from: location.pathname }} />;
  }

  return children;
}
