"use client";
import React, {useEffect} from "react";
import { useState } from "react";
import Faq from "react-faq-component";



export default function CGU() {


    const data = {
        title: "Conditions Générales d'Utilisation",
        rows: [
            {
                title: "Acceptation des Conditions Générales d'Utilisation",
                content:
                <>
                    En accédant au Service, vous reconnaissez avoir pris connaissance, compris et accepté les présentes conditions générales d'utilisation. Si vous n'acceptez pas ces conditions, veuillez ne pas utiliser le Service.
                </>
            },

            {
                title: "Utilisation du Service",
                content:
                    <>
                        Vous devez être âgé d'au moins 16 ans pour participer aux concours organisés par TheTipTop.
                        Vous êtes responsable de toutes les actions entreprises avec votre compte.
                        Vous vous engagez à utiliser le Service conformément aux règles établies et aux lois en vigueur.
                    </>
            },


            {
                title: "Compte Utilisateur",
                content:
                    <>
                        Vous devez créer un compte pour accéder à certaines fonctionnalités du Service.
                        Vous êtes responsable de la confidentialité de votre compte et de votre mot de passe.
                        Vous vous engagez à ne pas partager votre compte avec des tiers.
                    </>
            },
            {
                title: "Participation aux Concours",
                content:
                    <>
                        Les concours proposés sur TheTipTop sont régis par des règles spécifiques publiées sur le site. En participant, vous acceptez ces règles.
                        Les gagnants des concours seront informés conformément aux procédures définies dans les règles du concours.
                    </>
            },

            {
                title: "Propriété Intellectuelle",
                content:
                    <>
                        TheTipTop est propriétaire de l'intégralité des droits de propriété intellectuelle relatifs au Service et à son contenu.
                        Vous vous engagez à ne pas copier, reproduire, distribuer ou exploiter le contenu du Service sans autorisation.
                    </>
            },

            {
                title: "Limitation de Responsabilité",
                content:
                    <>
                        TheTipTop ne peut être tenu responsable des dommages directs ou indirects résultant de l'utilisation du Service.
                        TheTipTop ne garantit pas la disponibilité continue du Service et se réserve le droit de le suspendre ou de le modifier à tout moment.
                    </>
            },

            {
                title: "Modification des Conditions Générales d'Utilisation",
                content:
                    <>
                        TheTipTop se réserve le droit de modifier les présentes conditions générales d'utilisation à tout moment. Les modifications seront publiées sur le site et prendront effet immédiatement.
                    </>
            },

            {
                title: "Droit Applicable",
                content:
                    <>
                        Les présentes conditions générales d'utilisation sont régies par le droit français. Tout litige relatif à l'interprétation ou à l'exécution des présentes sera de la compétence exclusive des tribunaux français.
                    </>
            },

            {
                title: "Contact",
                content:
                    <>
                        Pour toute question concernant les présentes conditions générales d'utilisation, vous pouvez nous contacter à l'adresse suivante : contact@tiptop.com.
                    </>
            },




        ]
    }


    return (
        <>
            <div className={`my-4`}>
                <strong className={'mb-4'}>
                    En accédant et en utilisant le Service de TheTipTop ainsi que ses fonctionnalités connexes (ci-après dénommés "le Service"), vous acceptez les conditions générales d'utilisation énoncées ci-dessous.
                </strong>
            </div>

            <div className={`my-4`}>
                <Faq className={'mt-4'} data={data}/>
            </div>
        </>
    )
}
