import { router } from "@inertiajs/react";
import { useEffect, useRef, useState } from "react";
import Layout from "../../Layouts/Layout";
const Events = () => {
    const [isAccountDropdownOpen, setIsAccountDropdownOpen] = useState(false);
    const accountDropdownRef = useRef(null);
    const [user, setUser] = useState({});
    const [events, setEvents] = useState([]);
    const [category, setCategory] = useState("all");
    const [title, setTitle] = useState("");
    const [description, setDescription] = useState("");
    const [date, setDate] = useState(new Date().toISOString().split("T")[0]);
    const [price, setPrice] = useState("");
    const [status, setStatus] = useState("draft");
    const [id, setId] = useState(null);
    const [editMode, setEditMode] = useState(true);
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

        const fetchEvents = async () => {
            try {
                const response = await fetch(
                    "http://127.0.0.1:8000/api/events",
                    {
                        headers: {
                            Authorization: `Bearer ${token}`,
                        },
                    }
                );
                if (response.ok) {
                    const eventData = await response.json();
                    setEvents(eventData);
                    console.log("Events data:", eventData);
                } else {
                    router.visit("/unauthorized");
                }
            } catch (error) {
                console.log("Error fetching events data:", error);
                router.visit("/unauthorized");
            }
        };

        fetchEvents();
    }, [token]);

    const handleSubmit = async (e) => {
        e.preventDefault();
        if (!token) {
            router.visit("/unauthorized");
            return;
        }

        const eventData = {
            title,
            description,
            event_date: date.split("T")[0],
            category: category,
            price: Number(price),
            status,
        };

        try {
            if (editMode === false) {
                console.log("Creating new event:", eventData);
                const response = await fetch(
                    `http://127.0.0.1:8000/api/events`,
                    {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            Authorization: `Bearer ${token}`,
                        },
                        body: JSON.stringify(eventData),
                    }
                );

                if (response.ok) {
                    window.alert("Event created successfully!");
                    window.location.reload();
                } else {
                    router.visit("/unauthorized");
                }
            } else {
                const response = await fetch(
                    `http://127.0.0.1:8000/api/events/${id}`,
                    {
                        method: "PUT",
                        headers: {
                            "Content-Type": "application/json",
                            Authorization: `Bearer ${token}`,
                        },
                        body: JSON.stringify(eventData),
                    }
                );
                if (response.ok) {
                    const newEvent = await response.json();
                    // setEvents((prevEvents) => [...prevEvents, newEvent]);
                    // setTitle("");
                    // setDescription("");
                    // setDate(new Date().toISOString().split("T")[0]);
                    // setPrice("");
                    // setStatus("");
                    // document.getElementById(`event_modal_${id}`).close();
                    // setId(null);
                    window.alert("Event updated successfully!");
                    window.location.reload();
                } else {
                    router.visit("/unauthorized");
                }
            }
        } catch (error) {
            console.log("Error updating event:", error);
            router.visit("/unauthorized");
        }
    };
    const handleDelete = async () => {
        if (!token) {
            router.visit("/unauthorized");
            return;
        }

        try {
            const response = await fetch(
                `http://127.0.0.1:8000/api/events/${id}`,
                {
                    method: "DELETE",
                    headers: {
                        Authorization: `Bearer ${token}`,
                    },
                }
            );
            if (response.ok) {
                setEvents((prevEvents) =>
                    prevEvents.filter((event) => event.id !== id)
                );
                window.alert("Event deleted successfully!");
                window.location.reload();
            } else {
                router.visit("/unauthorized");
            }
        } catch (error) {
            console.log("Error deleting event:", error);
            router.visit("/unauthorized");
        }
    };

    return (
        <>
            <Layout user={user}>
                <div className="dashboard-container">
                    <div className="dashboard-header flex justify-between items-center p-4 bg-gray-100 border-b">
                        <div>
                            <h1 className="text-2xl font-bold">Events</h1>
                        </div>
                        <div>
                            <button
                                className="btn btn-primary"
                                onClick={() => {
                                    setEditMode(false);
                                    document
                                        .getElementById(`add_event_modal`)
                                        .showModal();
                                }}
                            >
                                Add New Event
                            </button>
                            <dialog
                                id={`add_event_modal`}
                                className="modal modal-bottom sm:modal-middle"
                            >
                                <div className="modal-box">
                                    <fieldset className="fieldset">
                                        <legend className="fieldset-legend">
                                            Event Title
                                        </legend>
                                        <input
                                            type="text"
                                            className="input w-full"
                                            placeholder="Type here"
                                            value={title}
                                            onChange={(e) =>
                                                setTitle(e.target.value)
                                            }
                                        />
                                    </fieldset>
                                    <fieldset className="fieldset">
                                        <legend className="fieldset-legend">
                                            Event Description
                                        </legend>
                                        <textarea
                                            className="textarea w-full"
                                            placeholder="Type here"
                                            value={description}
                                            onChange={(e) =>
                                                setDescription(e.target.value)
                                            }
                                        />
                                    </fieldset>
                                    <fieldset className="fieldset">
                                        <legend className="fieldset-legend">
                                            Event Date
                                        </legend>
                                        <input
                                            type="date"
                                            className="input w-full"
                                            value={date}
                                            onChange={(e) =>
                                                setDate(e.target.value)
                                            }
                                        />
                                    </fieldset>
                                    <fieldset className="fieldset">
                                        <legend className="fieldset-legend">
                                            Event Price
                                        </legend>
                                        <input
                                            type="number"
                                            className="input w-full"
                                            placeholder="Type here"
                                            value={price}
                                            onChange={(e) =>
                                                setPrice(e.target.value)
                                            }
                                        />
                                    </fieldset>
                                    <fieldset className="fieldset">
                                        <legend className="fieldset-legend">
                                            Event Status
                                        </legend>
                                        <select
                                            className="select w-full"
                                            value={status}
                                            onChange={(e) =>
                                                setStatus(e.target.value)
                                            }
                                        >
                                            <option value="draft">Draft</option>
                                            <option value="published">
                                                Published
                                            </option>
                                        </select>
                                    </fieldset>
                                    <div className="modal-action">
                                        <button
                                            className="btn btn-info text-white"
                                            onClick={handleSubmit}
                                        >
                                            Add Event
                                        </button>
                                        <form method="dialog">
                                            {/* if there is a button in form, it will close the modal */}
                                            <button
                                                className="btn"
                                                onClick={() => {
                                                    setTitle("");
                                                    setDescription("");
                                                    setDate("");
                                                    setPrice("");
                                                    setStatus("draft");
                                                    setId(null);
                                                    setEditMode(true);
                                                }}
                                            >
                                                Close
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </dialog>
                        </div>
                    </div>
                    {/* Dashboard content goes here */}
                    <div className="events-list mt-4">
                        {events.length > 0 ? (
                            events.map((event) => (
                                <div
                                    key={event.id}
                                    className="event-item p-4 mb-2 border rounded hover:bg-gray-200 cursor-pointer flex justify-between items-center"
                                >
                                    <div>
                                        <h2 className="text-xl font-semibold">
                                            {event.title}
                                        </h2>
                                        <p>
                                            {event.description.slice(0, 100)}...
                                        </p>
                                        <p>
                                            Date:{" "}
                                            {new Date(
                                                event.event_date
                                            ).toLocaleDateString()}
                                        </p>
                                    </div>
                                    <div>
                                        <button
                                            className="mt-2 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 cursor-pointer"
                                            onClick={() => {
                                                setTitle(event.title);
                                                setDescription(
                                                    event.description
                                                );
                                                setDate(
                                                    event.event_date.split(
                                                        " "
                                                    )[0]
                                                );
                                                setPrice(event.price);
                                                setStatus(event.status);
                                                setId(event.id);
                                                document
                                                    .getElementById(
                                                        `event_modal_${event.id}`
                                                    )
                                                    .showModal();
                                            }}
                                        >
                                            View Details
                                        </button>
                                        {/* Open the modal using document.getElementById('ID').showModal() method */}
                                        <button
                                            className="mt-2 ml-2 px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 cursor-pointer"
                                            onClick={() =>
                                                document
                                                    .getElementById(
                                                        `review_modal_${event.id}`
                                                    )
                                                    .showModal()
                                            }
                                        >
                                            See Reviews
                                        </button>
                                        <dialog
                                            id={`review_modal_${event.id}`}
                                            className="modal modal-bottom sm:modal-middle"
                                        >
                                            <div className="modal-box max-h-[80vh] overflow-y-auto">
                                                <h3 className="font-bold text-lg mb-4">
                                                    Client Reviews
                                                </h3>

                                                {event.reviews.length === 0 ? (
                                                    <p className="text-gray-500">
                                                        No reviews yet.
                                                    </p>
                                                ) : (
                                                    <div className="space-y-4">
                                                        {event.reviews.map(
                                                            (review, idx) => (
                                                                <div
                                                                    key={idx}
                                                                    className="border rounded-xl p-4 bg-gray-100 shadow"
                                                                >
                                                                    <div className="flex justify-between items-center">
                                                                        <p className="font-semibold">
                                                                            {review
                                                                                .user
                                                                                ?.name ??
                                                                                "Unknown User"}
                                                                        </p>
                                                                        <span className="text-yellow-500 font-medium">
                                                                            ⭐{" "}
                                                                            {
                                                                                review.rating
                                                                            }
                                                                            /5
                                                                        </span>
                                                                    </div>
                                                                    <p className="mt-2 text-gray-700">
                                                                        {
                                                                            review.comment
                                                                        }
                                                                    </p>
                                                                </div>
                                                            )
                                                        )}
                                                    </div>
                                                )}

                                                <div className="modal-action">
                                                    <form method="dialog">
                                                        <button className="btn btn-primary">
                                                            Close
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </dialog>
                                        <dialog
                                            id={`event_modal_${event.id}`}
                                            className="modal modal-bottom sm:modal-middle"
                                        >
                                            <div className="modal-box">
                                                <fieldset className="fieldset">
                                                    <legend className="fieldset-legend">
                                                        Event Title
                                                    </legend>
                                                    <input
                                                        type="text"
                                                        className="input w-full"
                                                        placeholder="Type here"
                                                        value={title}
                                                        onChange={(e) =>
                                                            setTitle(
                                                                e.target.value
                                                            )
                                                        }
                                                    />
                                                </fieldset>
                                                <fieldset className="fieldset">
                                                    <legend className="fieldset-legend">
                                                        Event Description
                                                    </legend>
                                                    <textarea
                                                        className="textarea w-full"
                                                        placeholder="Type here"
                                                        value={description}
                                                        onChange={(e) =>
                                                            setDescription(
                                                                e.target.value
                                                            )
                                                        }
                                                    />
                                                </fieldset>
                                                <fieldset className="fieldset">
                                                    <legend className="fieldset-legend">
                                                        Event Date
                                                    </legend>
                                                    <input
                                                        type="date"
                                                        className="input w-full"
                                                        value={date}
                                                        onChange={(e) =>
                                                            setDate(
                                                                e.target.value
                                                            )
                                                        }
                                                    />
                                                </fieldset>
                                                <fieldset className="fieldset">
                                                    <legend className="fieldset-legend">
                                                        Event Price
                                                    </legend>
                                                    <input
                                                        type="number"
                                                        className="input w-full"
                                                        placeholder="Type here"
                                                        value={price}
                                                        onChange={(e) =>
                                                            setPrice(
                                                                e.target.value
                                                            )
                                                        }
                                                    />
                                                </fieldset>
                                                <fieldset className="fieldset">
                                                    <legend className="fieldset-legend">
                                                        Event Status
                                                    </legend>
                                                    <select
                                                        className="select w-full"
                                                        value={status}
                                                        onChange={(e) =>
                                                            setStatus(
                                                                e.target.value
                                                            )
                                                        }
                                                    >
                                                        <option value="draft">
                                                            Draft
                                                        </option>
                                                        <option value="published">
                                                            Published
                                                        </option>
                                                    </select>
                                                </fieldset>
                                                <div className="modal-action">
                                                    <button
                                                        className="btn btn-info text-white"
                                                        onClick={handleSubmit}
                                                    >
                                                        Save Changes
                                                    </button>
                                                    <button
                                                        className="btn btn-error text-white"
                                                        onClick={handleDelete}
                                                    >
                                                        Delete Event
                                                    </button>
                                                    <form method="dialog">
                                                        {/* if there is a button in form, it will close the modal */}
                                                        <button
                                                            className="btn"
                                                            onClick={() => {
                                                                setTitle("");
                                                                setDescription(
                                                                    ""
                                                                );
                                                                setDate("");
                                                                setPrice("");
                                                                setStatus(
                                                                    "draft"
                                                                );
                                                                setId(null);
                                                            }}
                                                        >
                                                            Close
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </dialog>
                                    </div>
                                </div>
                            ))
                        ) : (
                            <p>No events found.</p>
                        )}
                    </div>
                </div>
            </Layout>
        </>
    );
};

export default Events;
