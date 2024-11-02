import React, {useEffect, useState} from 'react';
import styles from "@/styles/pages/dashboards/storeAdminDashboard.module.css";
import {Col, Form, Row, Spin, Tag, theme} from 'antd';
import Image from 'next/image';
import LevelOneImg from "@/assets/images/levels/level1.png";
import LevelTwoImg from "@/assets/images/levels/level2.png";
import LevelThreeImg from "@/assets/images/levels/level3.png";
import LevelFourImg from "@/assets/images/levels/level4.png";
import LevelFiveImg from "@/assets/images/levels/level5.png";
import LockIconImg from "@/assets/images/lock.png";
import TrophyIconImg from "@/assets/images/trophy.png";


import {getAllBadges, getClientBadges} from "@/app/api";
import LogoutService from "@/app/service/LogoutService";
import {GiftOutlined} from "@ant-design/icons";


interface DataType {
    'id' : string;
    'name' : string;
    'description' : string;
}



function BadgesListPage() {

    const {logoutAndRedirectAdminsUserToLoginPage} = LogoutService();

    const [data, setData] = useState<DataType[]>();
    const [loading, setLoading] = useState(false);
    const [totalBadgeCount, setTotalBadgeCount] = useState(0);

    function fetchData() {
        setLoading(true);
        getAllBadges().then((response) => {
            console.log('response : ', response);
            setData(response.badges);
            setTotalBadgeCount(response.badges.length);
            setLoading(false);
        }).catch((err) => {
            if (err.response) {
                if (err.response.status === 401) {
                    logoutAndRedirectAdminsUserToLoginPage();
                }
            } else {
                console.log(err.request);
            }
        })
    }

    useEffect(() => {
        fetchData();
    }, []);


    const [userRole , setUserRole] = useState<string | null>(null);

    useEffect(() => {
        setUserRole(localStorage.getItem('loggedInUserRole'));
    }, []);

    const [clientBadges , setClientBadges] = useState<DataType[]>([]);
    const [clientBadgesIdsChecked , setClientBadgesIdsChecked] = useState<string[]>([]);

    useEffect(() => {
        let userId = localStorage.getItem('loggedInUserId') ?? "";
        if (userRole == "ROLE_CLIENT" && userId != "") {
            getClientBadges(userId).then((response) => {
                let badgesIds = response.badges.map((badge :any) => {
                    return badge.id;
                });
                setClientBadgesIdsChecked(badgesIds);
                setClientBadges(response.badges);
            }).catch((err) => {
                if (err.response) {
                    if (err.response.status === 401) {
                        logoutAndRedirectAdminsUserToLoginPage();
                    }
                } else {
                    console.log(err.request);
                }
            });
        }

    }, [userRole]);


    const renderBadgeImage = (badgeId: string) => {
        switch (badgeId.toString()) {
            case "1":
                return (
                    <Image src={LevelOneImg} alt={"LevelOneImg"}></Image>
                );
            case "2":
                return (
                    <Image src={LevelTwoImg} alt={"LevelTwoImg"}></Image>
                );
            case "3":
                return (
                    <Image src={LevelThreeImg} alt={"LevelThreeImg"}></Image>
                );
            case "4":
                return (
                    <Image src={LevelFourImg} alt={"LevelFourImg"}></Image>
                );
            case "5":
                return (
                    <Image src={LevelFiveImg} alt={"LevelFiveImg"}></Image>
                );
            default:
                return (<></>);
        }
    }

    const renderBadges = () => {
        if (data) {
            return data.map((badge, key) => {
                let badgeId = badge.id;
                let badgeChecked = clientBadgesIdsChecked.includes(badgeId);

                let className = "";
                if (userRole == "ROLE_CLIENT") {
                    className = badgeChecked ? styles.badgeLevelCardChecked : styles.badgeLevelCardDisabled
                }


                let span = 12;
                if (badgeId == "1" || badgeId == "2" || badgeId == "3" || badgeId == "4") {
                    span = 12;
                }else {
                    span = 24;
                }
                let getTooltipStyle = (index: number) => {
                    switch (index-1) {
                        case 0:
                            return {
                                color: "#ffffff",
                                cursor: 'pointer',
                                backgroundColor: "#212227",
                                fontSize: 12,
                                marginLeft: 5,
                                marginRight: 5,
                            };
                        case 1:
                            return {
                                color: "#ffffff",
                                backgroundColor: "#E3E94B",
                                cursor: 'pointer',
                                fontSize: 12,
                                marginLeft: 5,
                                marginRight: 5,
                            };
                        case 2:
                            return {
                                color: "#ffffff",
                                backgroundColor: "#FFA400",
                                cursor: 'pointer',
                                fontSize: 12,
                                marginLeft: 5,
                                marginRight: 5,
                            };
                        case 3:
                            return {
                                color: "#ffffff",
                                cursor: 'pointer',
                                backgroundColor: "#7BC558",
                                fontSize: 12,
                                marginLeft: 5,
                                marginRight: 5,
                            };
                        case 4:
                            return {
                                color: "#ffffff",
                                backgroundColor: "#EBB3E6",
                                cursor: 'pointer',
                                fontSize: 12,
                                marginLeft: 5,
                                marginRight: 5,
                            };
                        default:
                            return {
                                color: "#ffffff",
                                backgroundColor: "#EBB3E6",
                                cursor: 'pointer',
                                fontSize: 12,
                                marginLeft: 5,
                                marginRight: 5,
                            };
                    }
                }


                return (
                    <Col key={key} className={`w-100 d-flex mt-5 justify-content-center`} xs={24} sm={24} md={span} lg={span} span={span}>
                        <div className={`${styles.badgeLevelCard}  ${className}  `}>

                            {userRole == "ROLE_CLIENT" && (
                                <>
                                    {badgeChecked && (
                                        <Image
                                            className={`${styles.checkedIcon}`}
                                            src={TrophyIconImg}
                                            alt={"TrophyIconImg"}
                                        >
                                        </Image>
                                    )}
                                    {!badgeChecked && (
                                        <Image
                                            className={`${styles.disabledIcon}`}
                                            src={LockIconImg}
                                            alt={"LockIconImg"}
                                        >
                                        </Image>
                                    )}

                                </>
                            )}





                            <div className={`${styles.ticketCardBody}`}>
                                <div className={`${styles.badgeLevelCardText} mb-1`}>



                                    <p className={`mt-5`}></p>
                                    
                                    <div className={`${styles.badgeLevelCardIcons}`}>
                                        {renderBadgeImage(badge.id)}
                                    </div>


                                    <div className={`${styles.badgeCardContent}`}>
                                        <p className={`${styles.badgeLabel}`}>
                                        <strong>
                                            {key+1}
                                        </strong>
                                            <span>
                                                 - {badge.name}
                                            </span>
                                        </p>
                                    </div>
                                    <div className={`${styles.badgeCardContent}`}>
                                        <p className={`${styles.badgeDescriptionLabel}`}>{badge.description}</p>
                                    </div>





                                </div>
                            </div>

                        </div>
                    </Col>

                )
            })
        }
    }



    return (
        <div className={styles.homePageContent}>

            <div className={`${styles.homePageContentTopHeader}`}>
                <h1 className={`mx-3`}>
                    Badges de récompenses
                </h1>
                <div className={`${styles.ticketsCardsMain}`}>

                    <div className={`${styles.ticketsCardsDiv} mb-5 px-4`}>

                        <Row className={`${styles.fullWidthElement}  mt-5 mb-5 w-100`}
                             gutter={{xs: 8, sm: 16, md: 24, lg: 32}}>

                            {loading &&
                                <div className={`${styles.loadingDashboardFullPage}`}>
                                    <Spin size="large"/>
                                </div>
                            }
                            {!loading && (
                               <>
                                   <Col key={"resultTikcets"} className={`w-100 d-flex justify-content-between mt-3 px-4`} xs={24} sm={24} md={24} lg={24} span={6}>



                                   </Col>
                                   {renderBadges()}

                                   {totalBadgeCount==0 && (
                                        <Col key={"noResultTikcets"} className={`w-100 d-flex justify-content-center mt-3 px-4`} xs={24} sm={24} md={24} lg={24} span={6}>
                                             <h6>
                                                  Aucun badge n'est disponible pour le moment.
                                             </h6>
                                        </Col>

                                   )}
                               </>
                               )}
                        </Row>


                    </div>

                </div>
            </div>


        </div>
    );
}

export default BadgesListPage;