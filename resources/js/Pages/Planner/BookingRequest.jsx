import { router } from "@inertiajs/react";
import { useEffect, useRef, useState } from "react";
import Layout from "../../Layouts/Layout";
const BookingRequest = () => {
    const [isAccountDropdownOpen, setIsAccountDropdownOpen] = useState(false);
    const accountDropdownRef = useRef(null);
    const [user, setUser] = useState({});
    const token = localStorage.getItem("token");
    const [requests, setRequests] = useState([]);
    const [requestId, setRequestId] = useState(null);
    const [requestTitle, setRequestTitle] = useState("");
    const [requestDescription, setRequestDescription] = useState("");
    const [requestDate, setRequestDate] = useState("");
    const [requestStatus, setRequestStatus] = useState("");
    const [requestClientName, setRequestClientName] = useState("");
    const [requestClientEmail, setRequestClientEmail] = useState("");
    const [requestPrice, setRequestPrice] = useState("");

    const acceptBookingRequest = async (id) => {
        try {
            const response = await fetch(
                `http://127.0.0.1:8000/api/accept-booking-request/${id}`,
                {
                    method: "GET",
                    headers: {
                        Authorization: `Bearer ${token}`,
                    },
                }
            );
            if (response.ok) {
                window.location.reload();
                const data = await response.json();
                console.log("Booking request accepted:", data);
                // Optionally, you can update the state to reflect the accepted request
            } else {
                console.log(
                    "Error accepting booking request:",
                    response.statusText
                );
            }
        } catch (error) {
            console.log("Error accepting booking request:", error);
        }
    };

    const rejectBookingRequest = async (id) => {
        try {
            const response = await fetch(
                `http://127.0.0.1:8000/api/reject-booking-request/${id}`,
                {
                    method: "GET",
                    headers: {
                        Authorization: `Bearer ${token}`,
                    },
                }
            );
            if (response.ok) {
                window.location.reload();
                const data = await response.json();
                console.log("Booking request rejected:", data);
                // Optionally, you can update the state to reflect the rejected request
            } else {
                console.log(
                    "Error rejecting booking request:",
                    response.statusText
                );
            }
        } catch (error) {
            console.log("Error rejecting booking request:", error);
        }
    };

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
    useEffect(() => {
        if (!token) {
            router.visit("/unauthorized");
            return;
        }

        const fetchRequests = async () => {
            try {
                const response = await fetch(
                    "http://127.0.0.1:8000/api/booking-requests",
                    {
                        headers: {
                            Authorization: `Bearer ${token}`,
                        },
                    }
                );
                if (response.ok) {
                    const requestsData = await response.json();
                    console.log("Fetched booking requests:", requestsData);
                    setRequests(requestsData);
                } else {
                    router.visit("/unauthorized");
                }
            } catch (error) {
                console.log("Error fetching booking requests:", error);
                router.visit("/unauthorized");
            }
        };

        fetchRequests();
    }, [token]);

    return (
        <>
            <Layout user={user}>
                <div className="dashboard-container">
                    <h1 className="text-2xl font-bold">Booking Request</h1>
                    <div className="mt-6">
                        {requests.length === 0 ? (
                            <p>No booking requests found.</p>
                        ) : (
                            <table className="min-w-full bg-white border border-gray-300">
                                <thead>
                                    <tr>
                                        <th className="py-2 px-4 border-b border-gray-300 text-left">
                                            Client Name
                                        </th>
                                        <th className="py-2 px-4 border-b border-gray-300 text-left">
                                            Event Date
                                        </th>
                                        <th className="py-2 px-4 border-b border-gray-300 text-left">
                                            Status
                                        </th>
                                        <th className="py-2 px-4 border-b border-gray-300 text-left"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {requests.map((request) => (
                                        <tr key={request.id}>
                                            <td className="py-2 px-4 border-b border-gray-300">
                                                {request.user.name}
                                            </td>
                                            <td className="py-2 px-4 border-b border-gray-300">
                                                {new Date(
                                                    request.event_date
                                                ).toLocaleDateString()}
                                            </td>
                                            <td className="py-2 px-4 border-b border-gray-300">
                                                {request.status}
                                            </td>
                                            <td className="py-2 px-4 border-b border-gray-300">
                                                <button
                                                    className="text-blue-500 hover:underline"
                                                    onClick={async () => {
                                                        await setRequestId(
                                                            request.id
                                                        );
                                                        await setRequestTitle(
                                                            request.title
                                                        );
                                                        await setRequestDescription(
                                                            request.description
                                                        );
                                                        await setRequestDate(
                                                            new Date(
                                                                request.event_date
                                                            ).toLocaleDateString()
                                                        );
                                                        await setRequestStatus(
                                                            request.status
                                                        );
                                                        await setRequestClientName(
                                                            request.user.name
                                                        );
                                                        await setRequestClientEmail(
                                                            request.user.email
                                                        );
                                                        await setRequestPrice(
                                                            request.price
                                                        );
                                                        document
                                                            .getElementById(
                                                                `my_modal_${request.id}`
                                                            )
                                                            .showModal();
                                                    }}
                                                >
                                                    View Details
                                                </button>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        )}
                        <dialog id={`my_modal_${requestId}`} className="modal">
                            <div className="modal-box">
                                <h3 className="font-bold text-lg">
                                    {requestTitle}
                                </h3>
                                <p className="py-4">{requestDescription}</p>
                                <p className="py-2">
                                    <strong>Event Date:</strong> {requestDate}
                                </p>
                                <p className="py-2">
                                    <strong>Status:</strong> {requestStatus}
                                </p>
                                <p className="py-2">
                                    <strong>Client Name:</strong>{" "}
                                    {requestClientName}
                                </p>
                                <p className="py-2">
                                    <strong>Client Email:</strong>{" "}
                                    {requestClientEmail}
                                </p>
                                <p className="py-2">
                                    <strong>Price:</strong> {requestPrice}
                                </p>
                                <div className="modal-action">
                                    <form
                                        method="dialog"
                                        className="flex gap-2"
                                    >
                                        {/* if there is a button in form, it will close the modal */}
                                        <button
                                            className="btn btn-success"
                                            onClick={async () => {
                                                await acceptBookingRequest(
                                                    requestId
                                                );
                                            }}
                                        >
                                            Accept
                                        </button>
                                        <button
                                            className="btn btn-error"
                                            onClick={async () => {
                                                await rejectBookingRequest(
                                                    requestId
                                                );
                                            }}
                                        >
                                            Reject
                                        </button>
                                        <button className="btn">Close</button>
                                    </form>
                                </div>
                            </div>
                        </dialog>
                    </div>
                </div>
            </Layout>
        </>
    );
};

export default BookingRequest;
