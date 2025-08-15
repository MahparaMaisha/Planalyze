import { router } from "@inertiajs/react";
import { AnimatePresence, motion } from "framer-motion";
import { useRef, useState } from "react";

const ClientLayout = ({ user, children }) => {
    const [isAccountDropdownOpen, setIsAccountDropdownOpen] = useState(false);
    const accountDropdownRef = useRef(null);

    const getUserInitials = (name = null) => {
        if (!name) return "U";
        return name
            .split(" ")
            .map((n) => n[0])
            .join("")
            .toUpperCase()
            .slice(0, 2);
    };

    const handleLogout = async () => {
        try {
            const token = localStorage.getItem("token");
            if (!token) {
                router.visit("/login");
                return;
            }
            const response = await fetch("http://127.0.0.1:8000/api/logout", {
                method: "POST",
                headers: {
                    Authorization: `Bearer ${token}`,
                },
            });
            if (response.ok) {
                localStorage.removeItem("token");
                router.visit("/login");
            } else {
                console.error("Logout failed:", response.statusText);
                // Fallback: redirect to login page
                window.alert("An error occurred while logging out. Please try again.");
            }
        } catch (error) {
            console.error("Logout error:", error);
            // Fallback: redirect to login page
            window.alert("An error occurred while logging out. Please try again.");
        }
    };

    const handleNavigation = (route) => {
        router.visit(route);
    };

    return (
        <div className="min-h-screen bg-base-200">
            {/* Navbar */}
            <div className="navbar bg-base-100 shadow-sm">
                <div className="navbar-start">
                    <div className="dropdown">
                        <div
                            tabIndex={0}
                            role="button"
                            className="btn btn-ghost btn-circle"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                className="h-5 w-5"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    strokeLinecap="round"
                                    strokeLinejoin="round"
                                    strokeWidth="2"
                                    d="M4 6h16M4 12h16M4 18h7"
                                />
                            </svg>
                        </div>
                        <ul
                            tabIndex={0}
                            className="menu menu-sm dropdown-content bg-base-100 rounded-box z-[1] mt-3 w-52 p-2 shadow"
                        >
                            <li>
                                <a
                                    onClick={() =>
                                        handleNavigation("/planner/dashboard")
                                    }
                                >
                                    Dashboard
                                </a>
                            </li>
                            <li>
                                <a
                                    onClick={() =>
                                        handleNavigation("/planner/events")
                                    }
                                >
                                    Events
                                </a>
                            </li>
                            <li>
                                <a
                                    onClick={() =>
                                        handleNavigation("/planner/account")
                                    }
                                >
                                    Profile
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div className="navbar-center">
                    <a
                        className="btn btn-ghost text-xl cursor-pointer"
                        onClick={() => handleNavigation("/dashboard")}
                    >
                        Planalize
                    </a>
                </div>
                <div className="navbar-end">
                    <div className="relative" ref={accountDropdownRef}>
                        <button
                            onClick={() =>
                                setIsAccountDropdownOpen(!isAccountDropdownOpen)
                            }
                            className="flex items-center space-x-2 text-gray-700 hover:text-gray-900 p-2 rounded-lg hover:bg-gray-100 transition-colors"
                        >
                            <div className="w-8 h-8 bg-primary rounded-full flex items-center justify-center">
                                <span className="text-white text-xs font-semibold">
                                    {getUserInitials(user?.name)}
                                </span>
                            </div>
                            <span className="hidden sm:block text-sm">
                                {user?.name || "Account"}
                            </span>
                            <svg
                                className="w-4 h-4"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    strokeLinecap="round"
                                    strokeLinejoin="round"
                                    strokeWidth={2}
                                    d="M19 9l-7 7-7-7"
                                />
                            </svg>
                        </button>

                        <AnimatePresence>
                            {isAccountDropdownOpen && (
                                <motion.div
                                    initial={{ opacity: 0, y: -10 }}
                                    animate={{ opacity: 1, y: 0 }}
                                    exit={{ opacity: 0, y: -10 }}
                                    transition={{ duration: 0.2 }}
                                    className="absolute right-0 mt-2 w-48 bg-base-100 rounded-lg shadow-xl border border-base-300 py-2 z-50"
                                >
                                    <div className="px-4 py-2 border-b border-base-300">
                                        <p className="text-sm text-gray-500">
                                            Signed in as
                                        </p>
                                        <p className="text-sm font-medium text-gray-900 truncate">
                                            {user?.name || "User Name"}
                                        </p>
                                    </div>
                                    <button
                                        onClick={() =>
                                            handleNavigation("/profile")
                                        }
                                        className="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:text-gray-900 hover:bg-gray-100 transition-colors"
                                    >
                                        <svg
                                            className="w-4 h-4 mr-3"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                strokeLinecap="round"
                                                strokeLinejoin="round"
                                                strokeWidth={2}
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                                            />
                                        </svg>
                                        Profile
                                    </button>
                                    <hr className="my-2 border-base-300" />
                                    <button
                                        onClick={handleLogout}
                                        className="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:text-red-700 hover:bg-red-50 transition-colors"
                                    >
                                        <svg
                                            className="w-4 h-4 mr-3"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                strokeLinecap="round"
                                                strokeLinejoin="round"
                                                strokeWidth={2}
                                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"
                                            />
                                        </svg>
                                        Logout
                                    </button>
                                </motion.div>
                            )}
                        </AnimatePresence>
                    </div>
                </div>
            </div>

            {/* Main Content */}
            <main className="container mx-auto px-4 py-6">{children}</main>
        </div>
    );
};

export default ClientLayout;
