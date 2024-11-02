import React, {useState , useEffect} from 'react';
import { Card, Col, Row } from 'antd';
import styles from "@/styles/pages/dashboards/storeAdminDashboard.module.css";
import "@/styles/pages/dashboards/dashboardStoreCards.css";
import {getStoresForAdmin} from '@/app/api';
import Image from "next/image";
import StoreImg from "@/assets/images/store.png";
import StoreOpenImg from "@/assets/images/store-open.png";
import StoreCloseImg from "@/assets/images/store-close.png";
import UsersImg from "@/assets/images/users.png";
import ClientsImg from "@/assets/images/clients.png";
import RouletteImg from "@/assets/images/roulette.png";




interface storeCardsProps {
    isStoresUpdated: boolean;
}

function StoreCards({isStoresUpdated}: storeCardsProps) {

    const [openStoresCount, setOpenStoresCount] = useState(0);
    const [closedStoresCount, setClosedStoresCount] = useState(0);

    useEffect(() => {
        getStoresForAdmin().then((response) => {
            setOpenStoresCount(response.openStoresCount);
            setClosedStoresCount(response.closedStoresCount);
        }).catch((err) => {
            console.log(err);
        });
}, [isStoresUpdated]);


        return (
           <>
               <div className={`${styles.homePageAdminCardsDiv} mb-5 px-4`}>

                   <Row className={`${styles.fullWidthElement} w-100`} gutter={{ xs: 8, sm: 16, md: 24, lg: 32 }} >
                       <Col className={`w-100 d-flex`} sm={24} md={12} lg={8} span={6}>
                           <div className={`${styles.topCardElement}`}>
                               <div className={`${styles.topCardElementIcon}`}>
                                   <Image src={StoreImg}  alt={"Nombres de magasins"}></Image>
                               </div>
                               <div className={`${styles.topCardElementText}`}>
                                   <div className={`${styles.counter}`}> {openStoresCount + closedStoresCount}</div>

                                   <div className={`${styles.cardTitle}`}>Nombres de magasins</div>
                               </div>
                           </div>
                       </Col>
                       <Col className={`w-100 d-flex`} sm={24} md={12} lg={8} span={6}>
                           <div className={`${styles.topCardElement}`}>
                               <div className={`${styles.topCardElementIcon}`}>
                                   <Image src={StoreOpenImg}  alt={"Magasins Ouverts"}></Image>
                               </div>
                               <div className={`${styles.topCardElementText}`}>
                                   <div className={`${styles.counter}`}> {openStoresCount}</div>

                                   <div className={`${styles.cardTitle}`}>Magasins Ouverts</div>
                               </div>
                           </div>
                       </Col>
                       <Col className={`w-100 d-flex`} sm={24} md={12} lg={8} span={6}>
                           <div className={`${styles.topCardElement}`}>
                               <div className={`${styles.topCardElementIcon}`}>
                                   <Image src={StoreCloseImg}  alt={"Magasins Fermés"}></Image>
                               </div>
                               <div className={`${styles.topCardElementText}`}>
                                   <div className={`${styles.counter}`}> {closedStoresCount}</div>

                                   <div className={`${styles.cardTitle}`}>Magasins Fermés</div>
                               </div>
                           </div>
                       </Col>


                   </Row>

               </div>
            </>
        );

}

export default StoreCards;