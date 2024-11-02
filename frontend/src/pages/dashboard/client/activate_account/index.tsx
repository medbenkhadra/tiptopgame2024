import React,{ useEffect , useState} from 'react'
import {Col, Row} from "react-bootstrap";
import styles from "@/styles/pages/auth/adminsLoginPage.module.css";
import {checkActivationTokenValidityClient} from "@/app/api";
import SpinnigLoader from "@/app/components/widgets/SpinnigLoader";

import {Modal} from "antd";



interface OptionType {
    token: string;
    email: string;
}

export default function index() {

    const [params, setParams] = useState<OptionType>({
        token: '',
        email: ''
    });

    const [loading, setLoading] = useState<boolean>(true);


    useEffect(() => {
        const urlParams = new URLSearchParams(window.location.search);
        const token = urlParams.get('token');
        const email = urlParams.get('email');
        setParams({
            token: token ? token : '',
            email: email ? email : ''
        });
    }, []);


    function checkTokenValidity() {
        checkActivationTokenValidityClient(params).then((response) => {
            setLoading(false);
            Modal.success({
                title: 'Compte activé',
                content: 'Votre compte a été activé avec succès',
            });

            setTimeout(() => {
                window.location.href = '/dashboard/client';
            });

        }).catch((error) => {
            Modal.error({
                title: 'Erreur est survenue',
                content: 'Le lien d\'activation est invalide ou a expiré',
            });

            setTimeout(() => {
                window.location.href = '/client_login';
            });
        })
    }

    useEffect(() => {
        if (params.token !== '' && params.email !== ''){
            checkTokenValidity();
        }
    }, [params]);


    return (
        <div>
            {loading && (
                <>
                    <SpinnigLoader/>
                </>
            )}
        </div>
    )
}
