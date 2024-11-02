import React, {useEffect, useState} from 'react';
import styles from "@/styles/pages/dashboards/storeAdminDashboard.module.css";
import {Col, Form, Row, Tag, theme} from 'antd';
import Image from 'next/image';
import InfuserImg from "@/assets/images/infuser.png";
import TeaBoxImg from "@/assets/images/teaBox.png";
import TeaBoxSignatureImg from "@/assets/images/teaBoxSignature.png";
import SurprisePlusImg from "@/assets/images/surprisePlus.png";
import SurpriseBoxImg from "@/assets/images/surprise.png";
import {getPrizes} from "@/app/api";
import LogoutService from "@/app/service/LogoutService";
import {GiftOutlined} from "@ant-design/icons";


interface DataType {
    'id' : string;
    'label' : string;
    'name' : string;
    'type' : string;
    'prize_value' : string;
    'winning_rate' : string;
    'totalCount' : string;
    'percentage' : string;
}

interface PrizeType {
    'id' : string;
    'label' : string;
    'name' : string;
    'type' : string;
    'prize_value' : string;
    'winning_rate' : string;
    'totalCount' : string;
    'percentage' : string;
}


function PrizesList() {

    const {logoutAndRedirectAdminsUserToLoginPage} = LogoutService();

    const [data, setData] = useState<DataType[]>();
    const [loading, setLoading] = useState(false);
    const [totalPrizeCount, setTotalPrizeCount] = useState(0);

    function fetchData() {
        setLoading(true);
        getPrizes().then((response) => {
            console.log('response : ', response);
            setData(response.prizes);
            setTotalPrizeCount(response.prizes.length);
            //setLoading(false);
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



    const renderPrizeImage = (prizeId: string) => {
        switch (prizeId.toString()) {
            case "1":
                return (
                    <Image src={InfuserImg} alt={"Infuseur"}></Image>
                );
            case "2":
                return (
                    <Image src={TeaBoxImg} alt={"Infuseur"}></Image>
                );
            case "3":
                return (
                    <Image src={TeaBoxSignatureImg} alt={"Infuseur"}></Image>
                );
            case "4":
                return (
                    <Image src={SurpriseBoxImg} alt={"Infuseur"}></Image>
                );
            case "5":
                return (
                    <Image src={SurprisePlusImg} alt={"Infuseur"}></Image>
                );
            default:
                return (<></>);
        }
    }

    const renderPrizes = () => {
        if (data) {
            return data.map((prize, key) => {
                return (
                    <Col key={key} className={`w-100 d-flex mt-5`} xs={24} sm={24} md={12} lg={8} span={8}>
                        <div className={`${styles.ticketCardElement}`}>

                            <div className={`${styles.ticketCardBody}`}>
                                <div className={`${styles.prizeCardText} mb-1`}>
                                    <p className={`${styles.prizesTag}
                                     ${prize.id=="1" && styles.firstPrize}
                                        ${prize.id=="2" && styles.secondPrize}
                                        ${prize.id=="3" && styles.thirdPrize}
                                        ${prize.id=="4" && styles.fourthPrize}
                                        ${prize.id=="5" && styles.fifthPrize}
                                   
                                     `}>
                                        {prize.id=="1" && (
                                            <Tag icon={<GiftOutlined />} color="success">
                                               Gain ! N° {(prize.id.toString())}
                                            </Tag>

                                        )}
                                        {prize.id=="2" && (
                                            <Tag icon={<GiftOutlined />} color="success">
                                                Gain ! N° {(prize.id.toString())}
                                            </Tag>

                                        )}

                                        {prize.id=="3" && (
                                            <Tag icon={<GiftOutlined />} color="success">
                                                Gain ! N° {(prize.id.toString())}
                                            </Tag>

                                        )}
                                        {prize.id=="4" && (
                                            <Tag icon={<GiftOutlined />} color="success">
                                                Gain ! N° {(prize.id.toString())}
                                            </Tag>

                                        )}
                                        {prize.id=="5" && (
                                            <Tag icon={<GiftOutlined />} color="success">
                                                Gain ! N° {(prize.id.toString())}
                                            </Tag>

                                        )}


                                    </p>



                                    <p className={`my-3`}></p>
                                    <p><strong>Gain:</strong></p>
                                    <div className={`${styles.ticketCardIconsPrize}`}>
                                        {renderPrizeImage(prize.id)}
                                    </div>
                                    <p className={`${styles.prizeLabel}`}>{prize.label}</p>


                                    <div className={`${styles.prizeCardFooter}`}>
                                        <p className={`${styles.prizeInfo}`}>Pourcentage De Gain</p>
                                        <p className={`${styles.prizeInfoCounters}`}>{prize.percentage}%</p>
                                    </div>


                                </div>
                            </div>

                        </div>
                    </Col>

                )
            })
        }
    }



    const {token} = theme.useToken();
    const [form] = Form.useForm();
    const [expand, setExpand] = useState(false);

    const formStyle: React.CSSProperties = {
        maxWidth: 'none',
        background: token.colorFillAlter,
        borderRadius: token.borderRadiusLG,
        padding: 24,
    };









    useEffect(() => {
        setLoading(true);
        setTimeout(() => {
            setLoading(false);
        }, 1000);
    }, []);


    return (

        <div className={styles.homePageContent}>
            <div className={`${styles.homePageContentTopHeader}`}>
                <div className={`${styles.ticketsCardsMain}`}>
                    {!loading && (
                        <>
                            <h1 className={`${styles.ticketsCardsTitle}`}>
                                Liste des Gains
                            </h1>
                        </>
                    )}


                    <div className={`${styles.ticketsCardsDiv} mb-5 px-4`}>

                        <Row className={`${styles.fullWidthElement}  mt-0 mb-5 w-100`}
                             gutter={{xs: 8, sm: 16, md: 24, lg: 32}}>
                            {!loading && (
                               <>
                                   <Col key={"resultTikcets"} className={`w-100 d-flex justify-content-between mt-0 px-4`} xs={24} sm={24} md={24} lg={24} span={6}>



                                   </Col>
                                   {renderPrizes()}

                                   {totalPrizeCount==0 && (
                                        <Col key={"noResultTikcets"} className={`w-100 d-flex justify-content-center mt-0 px-4`} xs={24} sm={24} md={24} lg={24} span={6}>
                                             <h6>
                                                  Aucun Gain trouvé !
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

export default PrizesList;