"use client";
import React, {useEffect} from "react";
import { useState } from "react";
import Faq from "react-faq-component";



export default function PoliticsTab() {


    const data = {
        title: "Politique de confidentialité",
        rows: [
            {
                title: "Collecte d'informations personnelles",
                content: "Lorsque vous visitez notre site, nous recueillons automatiquement diverses informations relatives à votre appareil, telles que le type de navigateur, l'adresse IP, le fuseau horaire et certains cookies. De plus, nous collectons des données sur les pages ou produits que vous consultez, ainsi que sur les sites web ou les termes de recherche qui vous ont dirigé vers notre site. Toutes ces données collectées automatiquement sont regroupées sous le terme \"Informations sur l'appareil\"."
            },
            {
                title: "Comment utilisons-nous vos données personnelles ?",
                content: <>
                    Les informations que nous recueillons sur vos commandes sont principalement utilisées pour traiter vos achats effectués sur notre site. Cela inclut le traitement de vos informations de paiement, l'organisation de l'expédition de votre commande, ainsi que la fourniture de factures et de confirmations de commande. De plus, nous utilisons ces informations pour communiquer avec vous, détecter les fraudes ou les risques potentiels, et, conformément à vos préférences, vous envoyer des informations ou des publicités concernant nos produits ou services.

                    <br/> <br/>

                    Quant aux informations sur votre appareil, notamment votre adresse IP, nous les utilisons pour évaluer les risques de fraude ou de sécurité potentiels. Elles nous permettent également d'améliorer et d'optimiser notre site en analysant la manière dont nos clients naviguent et interagissent avec celui-ci. En outre, elles nous aident à évaluer l'efficacité de nos campagnes publicitaires et marketing.
                </>
            },
            {
                title: "Partage de vos données personnelles",
                content: <>
                    Dans certaines circonstances, il est possible que nous partagions vos informations personnelles afin de nous conformer aux lois et réglementations en vigueur, de répondre à une assignation, à un mandat de perquisition ou à toute autre demande légale de renseignements que nous pourrions recevoir, ou encore pour protéger nos droits.
                </>
            },
            {
                title: "Vos droits en tant qu'utilisateur du site web",
                content: <>
Si vous êtes un résident européen, vous avez le droit d'accéder aux informations personnelles que nous détenons à votre sujet et de demander que vos informations personnelles soient corrigées, mises à jour ou supprimées. Si vous souhaitez exercer ce droit, veuillez nous contacter via les coordonnées fournies ci-dessous.

                    <br/> <br/>

                    De plus, si vous êtes un résident européen, veuillez noter que nous traitons vos informations afin de remplir les contrats que nous pourrions avoir avec vous (par exemple si vous passez une commande sur notre site), ou encore pour poursuivre nos intérêts commerciaux légitimes énumérés ci-dessus. Veuillez noter que vos informations seront transférées en dehors de l'Europe, y compris aux États-Unis.

                    <br/> <br/>

                    Enfin, veuillez noter que nous conservons vos informations aussi longtemps que nécessaire pour vous fournir nos services et conformément à nos obligations légales et réglementaires. Par exemple, nous conservons vos données de commande pour une durée de 5 ans pour des raisons fiscales et comptables. Ces données sont ensuite archivées.


                </>
            },


            {
                title: "Notre politique de rétention des données",
                content: <>
                    Lorsque vous passez une commande sur notre site, nous conservons vos informations de commande pour nos dossiers, sauf si et jusqu'à ce que vous nous demandiez de les supprimer. Pour des raisons fiscales et comptables, nous devons conserver ces informations pendant une durée de 5 ans. Une fois cette période écoulée, nous supprimerons vos données de nos bases de données.
                </>
            },

            {
                title: "Respect de la vie privée des mineurs",
                content: <>
                    Le site n'est pas destiné aux personnes de moins de 16 ans.
                </>
            },

            {
                title: "Modifications",
                content: <>
                    Nous nous réservons le droit de modifier cette politique de confidentialité à tout moment. Les modifications et les clarifications prendront effet immédiatement après leur publication sur le site web. Si nous apportons des modifications à cette politique, nous vous informerons ici de leur mise à jour, afin que vous sachiez quelles informations nous recueillons, comment nous les utilisons et dans quelles circonstances nous les divulguons, le cas échéant.
                </>
            },


            {
                title: "Contactez-nous",
                content: <>
                    Pour plus d'informations sur notre politique de confidentialité, si vous avez des questions ou si
                    vous souhaitez faire une réclamation, veuillez nous contacter par e-mail à contact@tiptop.com

                    <br/> <br/>

                    Vous pouvez également nous contacter par courrier postal à l'adresse suivante : 18 rue Léon Frot,
                    75011 Paris

                </>
            }

        ]
    }


    return (
        <>
            <div className={`my-4`}>
                <strong className={'mb-4'}>
                    Cette Politique de confidentialité explique comment vos informations personnelles sont collectées,
                    utilisées et partagées lorsque vous visitez ou effectuez un achat sur notre site web,
                    www.thetiptop.com (le « Site »).
                </strong>
            </div>

            <div className={`my-4`}>
                <Faq className={'mt-4'} data={data}/>
            </div>
        </>
    )
}
