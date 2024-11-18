import React, {useEffect, useState} from 'react';
import styles from "@/styles/pages/dashboards/storeAdminDashboard.module.css";


function TirageAuSortTemplate() {



        return (
        <div className={styles.homePageContent}>

            <div className={`${styles.homePageContentTopHeader}`}>
                <h1 className={`mx-3`}>
                    Tirage au sort final
                </h1>
                <div className={`${styles.ticketsCardsMain} mt-5`}>
                    <div className={`${styles.ticketsCardsDiv} ${styles.correspandancesDiv} mb-5 px-4`}>
                        <div className={` w-100 ${styles.templatesPersoDiv}`}>
                            <h5 className={"mt-5"}>
                                Veuillez procéder au tirage au sort final en utilisant le profil huissier
                            </h5>

                            <br/>
                            <ul>
                                <li>
                                    <span>
                                        E-mail de l'huissier: <strong>
                                        rick.arnaud@dsp5-archi-f23-15m-g7.ovh
                                    </strong>
                                    </span>
                                </li>

                                <br/>

                                <li>
                                    <span>
                                        Mot de passe: <strong >
                                        mohamed6759F@
                                    </strong>
                                    </span>
                                </li>

                            </ul>


                        </div>

                    </div>

                </div>
            </div>


        </div>
        );
}

export default TirageAuSortTemplate;