import { router } from "@inertiajs/react";
import { useEffect, useRef, useState } from "react";
import ClientLayout from "../../Layouts/ClientLayout";

const Dashboard = () => {
    const [isAccountDropdownOpen, setIsAccountDropdownOpen] = useState(false);
    const accountDropdownRef = useRef(null);
    const [user, setUser] = useState({});
    const [planners, setPlanners] = useState([]);
    const [allPlanners, setAllPlanners] = useState([]); // Store all planners for filtering
    const [reviewData, setReviewData] = useState({});
    const [isSubmittingReview, setIsSubmittingReview] = useState({});
    const [searchTerm, setSearchTerm] = useState("");
    const [isSearching, setIsSearching] = useState(false);
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

        const fetchPlanners = async () => {
            try {
                const response = await fetch(
                    "http://127.0.0.1:8000/api/get-top-rated-planners",
                    {
                        headers: {
                            Authorization: `Bearer ${token}`,
                        },
                    }
                );
                if (response.ok) {
                    const plannerData = await response.json();
                    setPlanners(plannerData.planners);
                    setAllPlanners(plannerData.planners); // Store all planners
                    console.log("Planners fetched successfully:", plannerData);
                } else {
                    router.visit("/unauthorized");
                }
                console.log("Planners fetched successfully:", planners);
            } catch (error) {
                console.log("Error fetching user data:", error);
                router.visit("/unauthorized");
            }
        };

        fetchPlanners();
    }, [token]);

    const handleSearchChange = async (e) => {
        const term = e.target.value;
        setSearchTerm(term);

        if (term.trim() === "") {
            // If search is empty, show all planners
            setPlanners(allPlanners);
            return;
        }

        setIsSearching(true);

        try {
            const response = await fetch(
                `http://127.0.0.1:8000/api/planner-search?q=${encodeURIComponent(
                    term
                )}`,
                {
                    headers: {
                        Authorization: `Bearer ${token}`,
                    },
                }
            );

            if (response.ok) {
                const searchResults = await response.json();
                const filteredPlanners = allPlanners.filter((planner) =>
                    planner.name.toLowerCase().includes(term.toLowerCase())
                );
                setPlanners(filteredPlanners);
            } else {
                console.error("Search failed");
                // On error, fall back to local search
                const localFiltered = allPlanners.filter((planner) =>
                    planner.name.toLowerCase().includes(term.toLowerCase())
                );
                setPlanners(localFiltered);
            }
        } catch (error) {
            console.error("Search error:", error);
            // On error, fall back to local search
            const localFiltered = allPlanners.filter((planner) =>
                planner.name.toLowerCase().includes(term.toLowerCase())
            );
            setPlanners(localFiltered);
        } finally {
            setIsSearching(false);
        }
    };

    const calculateNewAverage = (existingReviews, newRating) => {
        const totalReviews = existingReviews.length + 1;
        const currentSum = existingReviews.reduce(
            (sum, review) => sum + review.rating,
            0
        );
        return ((currentSum + newRating) / totalReviews).toFixed(1);
    };

    const handleReviewChange = (plannerId, field, value) => {
        setReviewData((prev) => ({
            ...prev,
            [plannerId]: {
                ...prev[plannerId],
                [field]: value,
            },
        }));
    };

    const submitReview = async (plannerId) => {
        const review = reviewData[plannerId];

        if (!review || !review.rating || !review.comment) {
            alert("Please provide both rating and comment");
            return;
        }

        setIsSubmittingReview((prev) => ({ ...prev, [plannerId]: true }));

        try {
            const response = await fetch(
                "http://127.0.0.1:8000/api/leave-review",
                {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        Authorization: `Bearer ${token}`,
                    },
                    body: JSON.stringify({
                        planner_id: plannerId,
                        rating: parseInt(review.rating),
                        comment: review.comment,
                    }),
                }
            );

            if (response.ok) {
                const result = await response.json();
                alert("Review submitted successfully!");

                // Update the planners state to include the new review
                setPlanners((prev) =>
                    prev.map((planner) => {
                        if (planner.id === plannerId) {
                            const newReview = {
                                ...result.review,
                                user: { name: user.name },
                            };
                            return {
                                ...planner,
                                reviews: [
                                    ...(planner.reviews || []),
                                    newReview,
                                ],
                                average_rating: calculateNewAverage(
                                    planner.reviews || [],
                                    result.review.rating
                                ),
                            };
                        }
                        return planner;
                    })
                );

                // Clear the form
                setReviewData((prev) => ({
                    ...prev,
                    [plannerId]: { rating: "", comment: "" },
                }));

                // Close the modal
                document.getElementById(`my_modal_${plannerId}`).close();
            } else {
                const error = await response.json();
                alert(error.message || "Failed to submit review");
            }
        } catch (error) {
            console.error("Error submitting review:", error);
            alert("An error occurred while submitting the review");
        } finally {
            setIsSubmittingReview((prev) => ({ ...prev, [plannerId]: false }));
        }
    };

    return (
        <>
            <ClientLayout user={user}>
                <div className="dashboard-container">
                    <h1 className="text-2xl font-bold">Dashboard</h1>
                    <div className="mt-4">
                        <h2 className="text-xl font-semibold">
                            Welcome, {user.name}
                        </h2>
                        <p className="text-gray-600">
                            Role: {user.role_id === 1 ? "Planner" : "Client"}
                        </p>
                    </div>
                    <div className="mt-6">
                        <div className="flex flex-col md:flex-row md:justify-between items-start md:items-center mb-4 gap-4">
                            <h2 className="text-xl font-semibold">
                                Top Rated Planners ({planners.length})
                            </h2>

                            {/* Search Input */}
                            <div className="relative w-full md:w-80">
                                <input
                                    type="text"
                                    placeholder="Search planners..."
                                    value={searchTerm}
                                    onChange={handleSearchChange}
                                    className="w-full px-4 py-2 pl-10 pr-4 bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                />

                                {/* Search Icon */}
                                <svg
                                    className="absolute left-3 top-2.5 h-5 w-5 text-gray-400"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        strokeLinecap="round"
                                        strokeLinejoin="round"
                                        strokeWidth={2}
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                                    />
                                </svg>

                                {/* Loading indicator */}
                                {isSearching && (
                                    <div className="absolute right-3 top-2.5">
                                        <div className="w-5 h-5 border-2 border-blue-500 border-t-transparent rounded-full animate-spin"></div>
                                    </div>
                                )}
                            </div>
                        </div>

                        {/* No results message */}
                        {searchTerm &&
                            planners.length === 0 &&
                            !isSearching && (
                                <div className="text-center py-8">
                                    <p className="text-gray-600">
                                        No planners found for "{searchTerm}".
                                        Try a different search term.
                                    </p>
                                </div>
                            )}

                        <ul className="list bg-base-100 rounded-box shadow-md">
                            {planners.map((planner) => (
                                <li
                                    key={planner.id}
                                    className="list-row flex justify-between cursor-pointer hover:bg-gray-100 p-3"
                                    onClick={() =>
                                        document
                                            .getElementById(
                                                `my_modal_${planner.id}`
                                            )
                                            .showModal()
                                    }
                                >
                                    <div>
                                        <div>{planner.name}</div>
                                        <div className="text-xs font-semibold opacity-60">
                                            Rating: {planner.average_rating}
                                        </div>
                                    </div>
                                    <div>
                                        <dialog
                                            id={`my_modal_${planner.id}`}
                                            className="modal"
                                        >
                                            <div className="modal-box max-w-2xl">
                                                <h3 className="font-bold text-lg mb-4">
                                                    {planner.name}
                                                </h3>

                                                {/* Planner Bio Section */}
                                                <div className="mb-6">
                                                    <h4 className="font-semibold text-md mb-2">
                                                        About
                                                    </h4>
                                                    <p className="text-gray-700">
                                                        {planner.planner?.bio ||
                                                            "No bio available."}
                                                    </p>
                                                </div>

                                                {/* Existing Reviews Section */}
                                                <div className="border-t pt-4 mb-6">
                                                    <h4 className="font-semibold text-md mb-3">
                                                        Reviews (
                                                        {planner.reviews
                                                            ?.length || 0}
                                                        )
                                                    </h4>

                                                    {planner.reviews &&
                                                    planner.reviews.length >
                                                        0 ? (
                                                        <div className="max-h-48 overflow-y-auto space-y-3">
                                                            {planner.reviews.map(
                                                                (
                                                                    review,
                                                                    index
                                                                ) => (
                                                                    <div
                                                                        key={
                                                                            review.id ||
                                                                            index
                                                                        }
                                                                        className="bg-gray-50 p-3 rounded-lg"
                                                                    >
                                                                        <div className="flex items-center justify-between mb-2">
                                                                            <div className="flex items-center gap-2">
                                                                                <span className="font-medium text-sm">
                                                                                    {review
                                                                                        .user
                                                                                        ?.name ||
                                                                                        "Anonymous"}
                                                                                </span>
                                                                                <div className="flex">
                                                                                    {[
                                                                                        1,
                                                                                        2,
                                                                                        3,
                                                                                        4,
                                                                                        5,
                                                                                    ].map(
                                                                                        (
                                                                                            star
                                                                                        ) => (
                                                                                            <span
                                                                                                key={
                                                                                                    star
                                                                                                }
                                                                                                className={`text-sm ${
                                                                                                    star <=
                                                                                                    review.rating
                                                                                                        ? "text-yellow-400"
                                                                                                        : "text-gray-300"
                                                                                                }`}
                                                                                            >
                                                                                                ★
                                                                                            </span>
                                                                                        )
                                                                                    )}
                                                                                </div>
                                                                            </div>
                                                                            <span className="text-xs text-gray-500">
                                                                                {new Date(
                                                                                    review.created_at
                                                                                ).toLocaleDateString()}
                                                                            </span>
                                                                        </div>
                                                                        <p className="text-sm text-gray-700">
                                                                            {
                                                                                review.comment
                                                                            }
                                                                        </p>
                                                                    </div>
                                                                )
                                                            )}
                                                        </div>
                                                    ) : (
                                                        <p className="text-gray-500 text-sm">
                                                            No reviews yet. Be
                                                            the first to leave a
                                                            review!
                                                        </p>
                                                    )}
                                                </div>

                                                {/* Leave Review Section */}
                                                <div className="border-t pt-4">
                                                    <h4 className="font-semibold text-md mb-3">
                                                        Leave a Review
                                                    </h4>

                                                    {/* Rating Selection */}
                                                    <div className="mb-4">
                                                        <label className="block text-sm font-medium text-gray-700 mb-2">
                                                            Rating *
                                                        </label>
                                                        <div className="flex gap-2">
                                                            {[
                                                                1, 2, 3, 4, 5,
                                                            ].map((star) => (
                                                                <button
                                                                    key={star}
                                                                    type="button"
                                                                    onClick={(
                                                                        e
                                                                    ) => {
                                                                        e.stopPropagation();
                                                                        handleReviewChange(
                                                                            planner.id,
                                                                            "rating",
                                                                            star
                                                                        );
                                                                    }}
                                                                    className={`text-2xl ${
                                                                        reviewData[
                                                                            planner
                                                                                .id
                                                                        ]
                                                                            ?.rating >=
                                                                        star
                                                                            ? "text-yellow-400"
                                                                            : "text-gray-300"
                                                                    } hover:text-yellow-400 transition-colors`}
                                                                >
                                                                    ★
                                                                </button>
                                                            ))}
                                                        </div>
                                                    </div>

                                                    {/* Comment Textarea */}
                                                    <div className="mb-4">
                                                        <label className="block text-sm font-medium text-gray-700 mb-2">
                                                            Comment *
                                                        </label>
                                                        <textarea
                                                            value={
                                                                reviewData[
                                                                    planner.id
                                                                ]?.comment || ""
                                                            }
                                                            onChange={(e) => {
                                                                e.stopPropagation();
                                                                handleReviewChange(
                                                                    planner.id,
                                                                    "comment",
                                                                    e.target
                                                                        .value
                                                                );
                                                            }}
                                                            onClick={(e) =>
                                                                e.stopPropagation()
                                                            }
                                                            className="textarea textarea-bordered w-full h-24 resize-none"
                                                            placeholder="Share your experience with this planner..."
                                                            maxLength={1000}
                                                        />
                                                        <div className="text-xs text-gray-500 mt-1">
                                                            {reviewData[
                                                                planner.id
                                                            ]?.comment
                                                                ?.length || 0}
                                                            /1000 characters
                                                        </div>
                                                    </div>

                                                    {/* Submit Review Button */}
                                                    <button
                                                        onClick={(e) => {
                                                            e.stopPropagation();
                                                            submitReview(
                                                                planner.id
                                                            );
                                                        }}
                                                        disabled={
                                                            isSubmittingReview[
                                                                planner.id
                                                            ]
                                                        }
                                                        className={`btn btn-primary mr-2 ${
                                                            isSubmittingReview[
                                                                planner.id
                                                            ]
                                                                ? "loading"
                                                                : ""
                                                        }`}
                                                    >
                                                        {isSubmittingReview[
                                                            planner.id
                                                        ]
                                                            ? "Submitting..."
                                                            : "Submit Review"}
                                                    </button>
                                                </div>

                                                <div className="modal-action">
                                                    <form method="dialog">
                                                        <button className="btn">
                                                            Close
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </dialog>
                                    </div>
                                </li>
                            ))}
                        </ul>
                    </div>
                </div>
            </ClientLayout>
        </>
    );
};

export default Dashboard;
