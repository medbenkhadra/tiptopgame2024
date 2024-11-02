"use strict";
import {useRouter} from "next/router";
import {useEffect, useState} from "react";
import {googleLoginCallBack} from "@/app/api";

const GoogleCallback = () => {
  const router = useRouter();
  const { code } = router.query;

  const [loading, setLoading] = useState(false);

  useEffect(() => {
    if (code) {
      setLoading(true);
      googleLoginCallBack(code).then((res) => {
        let user = res.user;
        let token = res.token;

        let message = res.message;
        let firstLogin = "false";

        if(message == "created"){
            firstLogin = "true";
        }


        localStorage.setItem('loggedInUserToken', token);
        localStorage.setItem('firstLoginClientStatus', firstLogin);
        localStorage.setItem("loggedInUserId", user.id);
        localStorage.setItem("loggedInUserEmail", user.email);
        localStorage.setItem("loggedInUserRole", user.role);
        localStorage.setItem("loggedInUser", JSON.stringify(user));


        let userStore = user.store;
        if(!userStore){
            window.location.href = '/dashboard/client/complete_profile';
        }else {
          window.location.href = '/dashboard/client';
        }

      }).catch((err) => {
        console.log(err);
        window.location.href = '/client_login';
      });
    }

  }, [code]);


  return (
    <div className="flex flex-col items-center justify-center h-screen">
      {loading && <p>Loading...</p>}
    </div>
  );
};

export default GoogleCallback;