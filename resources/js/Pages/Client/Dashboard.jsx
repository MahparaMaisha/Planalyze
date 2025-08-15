import { router } from '@inertiajs/react';
import { useEffect, useRef, useState } from 'react';
import ClientLayout from '../../Layouts/ClientLayout';
const Dashboard = () => {
  const [isAccountDropdownOpen, setIsAccountDropdownOpen] = useState(false);
  const accountDropdownRef = useRef(null);
  const [user,setUser] = useState({});
  const [planners,setPlanner] = useState([]);
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
              console.log("Error fetching user data:",error);
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
                const response = await fetch("http://127.0.0.1:8000/api/planners", {
                    headers: {
                        Authorization: `Bearer ${token}`,
                    },
                });
                if (response.ok) {
                    const plannerData = await response.json();
                    setPlanner(plannerData);
                } else {
                    router.visit("/unauthorized");
                }
                console.log("Planners fetched successfully:", planners);
            } catch (error) {
              console.log("Error fetching user data:",error);
                router.visit("/unauthorized");
            }
        };

        fetchPlanners();
    }, [token]);
  
  
  return (
    <>
    <ClientLayout user={user}>
    <div className="dashboard-container">
        <h1 className="text-2xl font-bold">Dashboard</h1>
        {/* Dashboard content goes here */}
      </div>
    </ClientLayout>
      
    </>
  )
}

export default Dashboard