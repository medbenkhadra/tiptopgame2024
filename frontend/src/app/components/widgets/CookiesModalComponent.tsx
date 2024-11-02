"use client";
import React, {useEffect, useState} from 'react';
import {Button, Modal} from 'antd';

export default function CookiesModalComponent() {
    const [isModalOpen, setIsModalOpen] = useState(false);


    const handleAccept = () => {
        localStorage.setItem('cookiesAccepted', 'true');
        setIsModalOpen(false);
    };

    const handleRefuse = () => {
        localStorage.setItem('cookiesAccepted', 'false');
        setIsModalOpen(false);
    };

    useEffect(() => {
        if (localStorage.getItem('cookiesAccepted') !== 'true') {
            setIsModalOpen(true);
        }
    }, []);

    return (
        <>
            <Modal
                title="🍪 Notification relative à l'utilisation des cookies"
                open={isModalOpen}
                onCancel={handleRefuse}
                maskClosable={false}
                footer={[
                    <Button className='refuse-button' key="refuse" onClick={handleRefuse}>
                        Refuser
                    </Button>,
                    <Button key="accept" type="primary" onClick={handleAccept}>
                        Accepter
                    </Button>,
                ]}
                className={"cookiesModal"}
            >
                <p>Nous souhaitons vous informer que notre site web utilise des cookies pour améliorer votre expérience
                    en ligne. Ces petits fichiers texte sont stockés sur votre appareil afin d'optimiser la navigation,
                    personnaliser le contenu, et analyser l'utilisation du site.</p>

                <p>En acceptant l'utilisation des cookies, vous consentez à ce que nous puissions les utiliser
                    conformément à notre politique de confidentialité et de cookies.</p>
                <p>Merci de faire partie de notre communauté en ligne. Pour plus d'informations, veuillez consulter
                    notre page de politique de confidentialité et de cookies.</p>
            </Modal>
        </>
    );
};
