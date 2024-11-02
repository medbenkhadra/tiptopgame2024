"use client";
import React, {useEffect} from "react";
import { useState } from "react";
import Faq from "react-faq-component";



export default function MentionsLegalesTab() {


    const data = {
        title: "Mentions légales",
        rows: [
            {
                title: "Informations sur l'éditeur du site thethetop.fr",
                content:
                <>
                    Le site thethetop.fr est édité et appartient exclusivement à la société SA Thé Tip Top. <br/> <br/>



                    <ul>
                        <li>Raison sociale : SA Thé Tip Top</li>
                        <li>Capital social : 100 000 €</li>
                        <li>Téléphone : +33 1 23 45 67 89</li>
                        <li>Adresse : 18 rue Léon Frot, 75011 Paris</li>
                        <li>Immatriculée au Registre du Commerce et des Sociétés de Paris sous le numéro : 761900043</li>
                        <li>Numéro de TVA intracommunautaire : FR27763827619</li>
                        <li>Adresse e-mail :contact@thetiptop.fr</li>
                        <li>Directeur de la publication : Eric Bourdon</li>
                        <li>Contact du responsable de la publication : eric.bourdon@gmail.com</li>
                    </ul>

                    <br/>


                </>
            },


            {
                title: "Site web - Hébergement - Hostinger",
                content:
                <>
                    Le site thethetop.fr est hébergé par Hostinger International Ltd. <br/> <br/>
                    <ul>
                        <li>Hostinger International Ltd.</li>
                        <li>61 Lordou Vironos Street</li>
                        <li>6023 Larnaca, Cyprus</li>
                        <li>Numéro de téléphone : +352 20 30 10 31</li>
                        <li>Adresse e-mail : xxxx@gmail.com</li>
                    </ul>
                    <br/>
                </>
            },


            {
                title: "Services proposés par le site thethetop.fr",
                content:
                    <>
                        Le site thethetop.fr a pour vocation de fournir des informations sur l'ensemble des activités de la société. Son propriétaire s'engage à offrir des informations aussi précises que possible sur le site thethetop.fr. Cependant, il ne saurait être tenu responsable des éventuelles omissions, inexactitudes ou lacunes dans la mise à jour des informations, qu'elles soient de son fait ou provenant de tiers partenaires fournissant ces données. Toutes les informations présentées sur le site thethetop.fr le sont à titre indicatif, ne sont pas exhaustives et peuvent faire l'objet de modifications. Elles sont fournies sous réserve des éventuelles évolutions intervenues depuis leur publication.
                    </>
            },

            {
                title: "Propriété intellectuelle et lutte contre les contrefaçons",
                content:
                    <>
                        Le propriétaire du site détient les droits de propriété intellectuelle sur l'ensemble des éléments accessibles sur le site, tels que les textes, images, graphismes, logos, icônes, sons et logiciels, ou dispose des droits d'usage les concernant. Toute reproduction, représentation, modification, publication ou adaptation totale ou partielle de ces éléments, par quelque moyen que ce soit, est strictement interdite, sauf autorisation écrite préalable obtenue par email à l'adresse : contact@thetiptop.fr. Toute utilisation non autorisée du site ou de l'un de ses éléments constituerait une contrefaçon et serait poursuivie conformément aux dispositions légales en vigueur, notamment aux articles L.335-2 et suivants du Code de la Propriété Intellectuelle.
                    </>
            },

            {
                title: "Cookies",
                content:
                    <>
                        Le site thethetop.fr peut être amené à vous demander l'acceptation des cookies pour des besoins de statistiques et d'affichage. Un cookie est une information déposée sur votre disque dur par le serveur du site que vous visitez. Il contient plusieurs données qui sont stockées sur votre ordinateur dans un simple fichier texte auquel un serveur accède pour lire et enregistrer des informations. Certaines parties du site thethetop.fr ne peuvent être fonctionnelles sans l'acceptation de cookies.
                    </>
            },

{
                title: "Liens hypertextes",
                content:
                    <>
                        Le site thethetop.fr peut contenir des liens hypertextes renvoyant vers d'autres sites internet. Cependant, le propriétaire du site ne dispose d'aucun moyen de contrôle sur le contenu des sites tiers. Il ne saurait donc être tenu responsable des éventuels contenus illicites des sites tiers. En outre, le propriétaire du site ne pourra être tenu responsable de tous dommages directs ou indirects résultant de l'utilisation des sites tiers.
                    </>
            },

            {
                title: "Protection des données personnelles",
                content:
                    <>
                        Conformément à la loi Informatique et Libertés du 6 janvier 1978 modifiée et au Règlement Général sur la Protection des Données (RGPD) n°2016/679 du 27 avril 2016, vous disposez d'un droit d'accès, de rectification, de portabilité, d'effacement de vos données personnelles ou une limitation du traitement. Vous pouvez vous opposer au traitement des données vous concernant et disposez du droit de retirer votre consentement à tout moment en vous adressant à l'adresse email : contact@tiptop.com ou par courrier postal à l'adresse suivante : 18 rue Léon Frot, 75011 Paris.
                    </>
            },

            {
                title: "Droit applicable et attribution de juridiction",
                content:
                    <>
                        Tout litige en relation avec l'utilisation du site thethetop.fr est soumis au droit français. En dehors des cas où la loi ne le permet pas, il est fait attribution exclusive de juridiction aux tribunaux compétents de Paris.
                    </>
            },

            {
                title: "Principales lois concernées",
                content:
                    <>
                        Loi n° 78-17 du 6 janvier 1978, notamment modifiée par la loi n° 2004-801 du 6 août 2004 relative à l'informatique, aux fichiers et aux libertés. <br/>
                        Loi n° 2004-575 du 21 juin 2004 pour la confiance dans l'économie numérique.
                    </>
            },

            {
                title: "Lexique",
                content:
                    <>
                        Utilisateur : Internaute se connectant, utilisant le site susnommé. <br/>
                        Informations personnelles : « les informations qui permettent, sous quelque forme que ce soit, directement ou non, l'identification des personnes physiques auxquelles elles s'appliquent » (article 4 de la loi n° 78-17 du 6 janvier 1978).
                    </>
            },




        ]
    }


    return (
        <>
            <div className={`my-4`}>
                <strong className={'mb-4'}>
                    Informations légales conformément à la loi pour la Confiance dans l'économie numérique (LCEN) <br/>
                    Conformément aux dispositions des articles 6-III et 19 de la Loi n° 2004-575 du 21 juin 2004 pour la Confiance dans l'économie numérique, également connue sous le nom de LCEN, nous souhaitons informer nos utilisateurs et visiteurs du site thethetop.fr des éléments suivants :
                </strong>
            </div>

            <div className={`my-4`}>
                <Faq className={'mt-4'} data={data}/>
            </div>
        </>
    )
}
