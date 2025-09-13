// import { router } from "@inertiajs/react";
// import axios from "axios";
// useState is a hook that allows you to have state variables in functional components
import { useState } from "react";
const Home = ({ message }) => {
    const [val1, setVal1] = useState(true);
    const [users, setUsers] = useState([]);

    const handlefunct = async (val) => {
        console.log("clicked");
        const response = await fetch("/api/momo-test");
        const data = await response.json();
        setUsers(data.users);
    };

    return (
        <>
            <button
                className="btn"
                onClick={() => {
                    document.getElementById("my_modal_3").showModal();
                    handlefunct();
                }}
            >
                open modal
            </button>
            <button
                className="btn"
                onClick={() => {
                    handlefunct();
                }}
            >
                Same
            </button>
            <div>
                {users.map((user) => (
                    <div key={user.id}>
                        {user.id} - {user.name} - {user.email}
                    </div>
                ))}
            </div>
            <dialog id="my_modal_3" className="modal">
                <div className="modal-box">
                    <form method="dialog">
                        {/* if there is a button in form, it will close the modal */}
                        <button className="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">
                            ✕
                        </button>
                    </form>
                    <h3 className="font-bold text-lg">Hello!</h3>
                    <p className="py-4">
                        Press ESC key or click on ✕ button to close
                    </p>
                </div>
            </dialog>
        </>
    );
};

export default Home;
