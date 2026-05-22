import { mountAdminDateRangePickers } from "./filament-admin-date-range";
import { mountAdminCountryDropdowns } from "./filament-admin-country-dropdown";
import { mountDashboardCountryFlags } from "./filament-admin-country-flags";
import { mountAdminPhoneInputs } from "./filament-admin-phone-input";
import { mountAdminTimePickers } from "./filament-admin-time-picker";

const mountAdminEnhancements = () => {
  mountDashboardCountryFlags();
  mountAdminDateRangePickers();
  mountAdminPhoneInputs();
  mountAdminTimePickers();
  mountAdminCountryDropdowns();
};

if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", mountAdminEnhancements, { once: true });
} else {
  mountAdminEnhancements();
}

document.addEventListener("livewire:navigated", mountAdminEnhancements);
document.addEventListener("livewire:initialized", () => {
  const livewire = (window as unknown as { Livewire?: { hook?: (name: string, callback: () => void) => void } }).Livewire;

  livewire?.hook?.("morphed", mountAdminEnhancements);
});
