import { router } from "@inertiajs/react";
import { useEffect, useState } from "react";
import ClientLayout from "../../Layouts/ClientLayout";

const Account = () => {
    const [user, setUser] = useState({});
    const [formData, setFormData] = useState({
        name: "",
        email: "",
    });

    const token = localStorage.getItem("token");

    useEffect(() => {
        if (!token) {
            router.visit("/unauthorized");
            return;
        }

        const fetchUser = async () => {
            try {
                const response = await fetch("http://127.0.0.1:8000/api/user", {
                    headers: {
                        Authorization: `Bearer ${token}`,
                    },
                });
                if (response.ok) {
                    const userData = await response.json();
                    setUser(userData);
                    setFormData({
                        name: userData.name || "",
                        email: userData.email || "",
                    });
                } else {
                    router.visit("/unauthorized");
                }
            } catch (error) {
                console.log("Error fetching user data:", error);
                router.visit("/unauthorized");
            }
        };

        fetchUser();
    }, [token]);

    const handleChange = (e) => {
        const { name, value } = e.target;
        setFormData((prev) => ({ ...prev, [name]: value }));
    };

    const handleSubmit = async (e) => {
        e.preventDefault();

        try {
            const response = await fetch(
                "http://127.0.0.1:8000/api/edit-profile",
                {
                    method: "PUT",
                    headers: {
                        "Content-Type": "application/json",
                        Authorization: `Bearer ${token}`,
                    },
                    body: JSON.stringify(formData),
                }
            );

            if (response.ok) {
                const updatedUser = await response.json();
                alert("Profile updated successfully!");
                setUser(updatedUser);
            } else {
                const err = await response.json();
                alert("Error: " + (err.message || "Something went wrong"));
            }
        } catch (error) {
            console.error("Update error:", error);
        }
    };

    const handleDelete = async () => {
        const confirmed = confirm(
            "Are you sure you want to delete your account?"
        );
        if (!confirmed) return;

        try {
            const response = await fetch(
                "http://127.0.0.1:8000/api/delete-account",
                {
                    method: "DELETE",
                    headers: {
                        Authorization: `Bearer ${token}`,
                    },
                }
            );

            if (response.ok) {
                alert("Account deleted.");
                localStorage.removeItem("token");
                router.visit("/login");
            } else {
                alert("Failed to delete account.");
            }
        } catch (error) {
            console.error("Delete error:", error);
        }
    };

    return (
        <ClientLayout user={user}>
            <div className="max-w-2xl mx-auto p-6 bg-gray-200/10 shadow rounded-lg mt-10">
                <h1 className="text-2xl font-bold mb-6">Account Information</h1>
                <form onSubmit={handleSubmit} className="space-y-4">
                    <div>
                        <label className="block text-gray-500">Name</label>
                        <input
                            name="name"
                            type="text"
                            value={formData.name}
                            onChange={handleChange}
                            className="w-full border p-2 rounded mt-1"
                            required
                        />
                    </div>
                    <div>
                        <label className="block text-gray-500">Email</label>
                        <input
                            name="email"
                            type="email"
                            value={formData.email}
                            onChange={handleChange}
                            className="w-full border p-2 rounded mt-1"
                            required
                        />
                    </div>

                    <div className="flex items-center justify-between mt-6">
                        <button
                            type="submit"
                            className="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded"
                        >
                            Save Changes
                        </button>
                        <button
                            type="button"
                            onClick={handleDelete}
                            className="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded"
                        >
                            Delete Account
                        </button>
                    </div>
                </form>
            </div>
        </ClientLayout>
    );
};

export default Account;
