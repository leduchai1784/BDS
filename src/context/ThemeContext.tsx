"use client";

import type React from "react";
import { createContext, useState, useContext, useEffect } from "react";

type Theme = "light" | "dark";

type ThemeContextType = {
  theme: Theme;
  toggleTheme: () => void;
  setThemeMode: (theme: Theme) => void;
};

const ThemeContext = createContext<ThemeContextType | undefined>(undefined);

export const ThemeProvider: React.FC<{
  children: React.ReactNode;
  initialTheme?: Theme;
}> = ({ children, initialTheme = "light" }) => {
  const [theme, setTheme] = useState<Theme>(initialTheme);

  useEffect(() => {
    // Sync document class and cookies on mount
    if (theme === "dark") {
      document.documentElement.classList.add("dark");
    } else {
      document.documentElement.classList.remove("dark");
    }
  }, [theme]);

  const updateTheme = (newTheme: Theme) => {
    setTheme(newTheme);
    try {
      localStorage.setItem("theme", newTheme);
      document.cookie = `theme=${newTheme}; path=/; max-age=31536000; SameSite=Lax`;
    } catch (e) {}
    if (newTheme === "dark") {
      document.documentElement.classList.add("dark");
    } else {
      document.documentElement.classList.remove("dark");
    }
  };

  const toggleTheme = () => {
    const nextTheme = theme === "light" ? "dark" : "light";
    updateTheme(nextTheme);
  };

  const setThemeMode = (newTheme: Theme) => {
    updateTheme(newTheme);
  };

  return (
    <ThemeContext.Provider value={{ theme, toggleTheme, setThemeMode }}>
      {children}
    </ThemeContext.Provider>
  );
};

export const useTheme = () => {
  const context = useContext(ThemeContext);
  if (context === undefined) {
    throw new Error("useTheme must be used within a ThemeProvider");
  }
  return context;
};
