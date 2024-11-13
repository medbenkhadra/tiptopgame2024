import React, {useEffect, useState} from 'react'
import {Button, Spin, Tag} from 'antd';
import { Container, Row, Col } from 'react-bootstrap';
import styles from '../../../styles/components/landingpage/landingPgaTopSection.module.css';
import wallpaperHomepageImg from '@/assets/images/wallpaperHomepage.png';
import Image from 'next/image';
import {getPrizes} from "@/app/api";
import InfuserImg from "@/assets/images/infuser.png";
import TeaBoxImg from "@/assets/images/teaBox.png";
import TeaBoxSignatureImg from "@/assets/images/teaBoxSignature.png";
import SurpriseBoxImg from "@/assets/images/surprise.png";
import SurprisePlusImg from "@/assets/images/surprisePlus.png";
import {GiftOutlined, InfoOutlined} from "@ant-design/icons";
import stylesAux from "@/styles/pages/dashboards/storeAdminDashboard.module.css";
import TopGameInfoBannerComponent from "@/app/components/widgets/TopGameInfoBannerComponent";

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

function LandingPageTopSection() {

    const [userRole, setUserRole] = useState('');
    const [user, setUser] = useState('');
    const [token, setToken] = useState('');
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        window.scrollTo(0, 0);

        const user = localStorage.getItem('loggedInUser');
        const token = localStorage.getItem('loggedInUserToken');
        const userRole = localStorage.getItem('loggedInUserRole');
        if (user) {
            setUser(JSON.parse(user));
        }
        if (token) {
            setToken(token);
        }
        if (userRole) {
            setUserRole(userRole);
        }
        setLoading(false);
    }, []);



    const [data, setData] = useState<DataType[]>();
    const [totalPrizeCount, setTotalPrizeCount] = useState(0);

    function fetchData() {
        setLoading(true);
        getPrizes().then((response) => {
            console.log('response : ', response);
            setData(response.prizes);
            setTotalPrizeCount(response.prizes.length);
            setLoading(false);
            window.scrollTo(0, 0);
        }).catch((err) => {
            if (err.response) {

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
                    <Col key={key} className={`mt-5`} md={4} sm={6} lg={4}>
                        <div className={`${stylesAux.ticketCardElement}`}>

                            <div className={`${stylesAux.ticketCardBody}`}>
                                <div className={`${stylesAux.prizeCardText} mb-1`}>
                                    <p className={`${stylesAux.prizesTag}
                                     ${prize.id=="1" && stylesAux.firstPrize}
                                        ${prize.id=="2" && stylesAux.secondPrize}
                                        ${prize.id=="3" && stylesAux.thirdPrize}
                                        ${prize.id=="4" && stylesAux.fourthPrize}
                                        ${prize.id=="5" && stylesAux.fifthPrize}
                                   
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
                                    <div className={`${stylesAux.ticketCardIconsPrize}`}>
                                        {renderPrizeImage(prize.id)}
                                    </div>
                                    <p className={`${stylesAux.prizeLabel}`}>{prize.label}</p>


                                    <div className={`${stylesAux.prizeCardFooter}`}>
                                        <p className={`${stylesAux.prizeInfo}`}>Nombre de Tickets</p>
                                        <p className={`${stylesAux.prizeInfo}`}>Nombre de Tickets Restant</p>
                                    </div>
                                    <div className={`${stylesAux.prizeCardFooter}`}>
                                        <p className={`${stylesAux.prizeInfoCounters}`}>{prize.totalCount}</p>
                                        <p className={`${stylesAux.prizeInfoCounters}`}>{prize.totalCount}</p>
                                    </div>
                                    <div className={`${stylesAux.prizeCardFooter}`}>
                                        <p className={`${stylesAux.prizeInfo}`}>Pourcentage De Gain</p>
                                        <p className={`${stylesAux.prizeInfoCounters}`}>{prize.percentage}%</p>
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
        <div className={`mx-2 px-1 ${styles.topSection} mt-1`} data-testid={"landing-page-top-section"}>
            <Row className="">
                <Col className={`${styles.topSectionLeftSide}`} md={12}>
                    <div className={`${styles.topSectionTextDiv}`}>
                        <h1>Bienvenue au Grand Jeu-Concours <span>  Thé Tip Top ! </span></h1>
                        <p>
                        Participez à notre aventure et gagnez des cadeaux exclusifs !
                         Scannez simplement votre ticket de caisse pour entrer dans la course et tentez de remporter des récompenses inoubliables.
                        </p>
                        {(userRole === 'ROLE_CLIENT' || userRole == "") && (
                            <>
                                <div className={`${styles.topSectionBtnsDiv}`}>
                                    <Button onClick={() => {
                                        window.location.href = "/dashboard/client"
                                    }} className={`landing-page-btn ${styles.playBtn} mt-2`} type={"default"}>Rejoignez
                                        l'aventure et Participez !</Button>

                                </div>
                            </>
                        )}

                        <div className={"my-5"}></div>

                        <h2>
                            <span>Le Thé Tip Top</span> vous souhaite bonne chance !
                        </h2>
                        <p>
                            <span>Le Thé Tip Top</span> est une entreprise familiale qui se spécialise dans la
                            production de thés bios et faits à la main. Notre mission est de vous offrir une expérience
                            de thé unique et inoubliable. Nous sommes fiers de vous présenter notre extraordinaire
                            Jeu-Concours, qui vous offre la chance de gagner des cadeaux exclusifs tout en découvrant
                            nos délicieux thés. Rejoignez-nous dès maintenant et tentez votre chance de gagner des
                            récompenses inoubliables !
                        </p>

                    </div>
                    <div className={`${styles.decorativeIcon} ${styles.teaLeafIcon}`}></div>
                    <div className={`${styles.decorativeIcon} ${styles.teaCupIcon}`}></div>
                    <div className={`${styles.decorativeIcon} ${styles.teaLIcon}`}></div>
                    <div className={`${styles.decorativeIcon} ${styles.teaCIcon}`}></div>
                    

                </Col>

                <TopGameInfoBannerComponent></TopGameInfoBannerComponent>
                <div className="container my-5">
                <section id="steps">
    <div className="text-center mb-5">
        <span>Guide</span>
        <h2 className="font-weight-bold display-4">
            Comment participer et
            <span style={{ color: '#D4AF37' }}> Gagner?</span>
        </h2>
    </div>
    <div className="row floating">
        {[1, 2, 3, 4].map((stepNumber, index) => (
            <div
                className={`col-sm-12 col-md-6 d-flex justify-content-center ${index % 2 === 0 ? 'text-left' : 'text-right'}`}
                key={stepNumber}
                style={{
                    display: 'flex',
                    justifyContent: index % 2 === 0 ? 'flex-start' : 'flex-end',
                    animation: 'fadeInUp 0.5s ease-out',
                }}
            >
                <div className="step-container position-relative p-4 my-5 shadow rounded" style={{ width: '100%', maxWidth: '300px', backgroundColor: '#F5F5DC' }}>
                    <div
                        className="step-icon font-weight-bold text-white rounded-circle d-flex align-items-center justify-content-center position-absolute"
                        style={{
                            width: '60px',
                            height: '60px',
                            top: '-30px',
                            left: index % 2 === 0 ? '-30px' : 'auto',
                            right: index % 2 === 1 ? '-30px' : 'auto',
                            backgroundColor: '#FF8C00',
                            border: '4px solid #F5F5DC',
                        }}
                    >
                        {stepNumber}
                    </div>
                    <div className="text-center">
                        <h4>
                            <span style={{ color: '#8B4513' }}>Étape {stepNumber}</span>
                        </h4>
                        <p className="font-weight-light my-3" style={{ color: '#8B0000' }}>
                            {stepNumber === 1 && (
                                <>
                                    <span role="img" aria-label="shop">🛒</span> Faites un saut dans notre boutique et laissez-vous tenter par un thé Tip Top, 
                                    soigneusement sélectionné pour éveiller vos sens. Une première gorgée vers la victoire !
                                </>
                            )}
                            {stepNumber === 2 && (
                                <>
                                    <span role="img" aria-label="ticket">🎟️</span> Conservez votre ticket de caisse comme un trésor ! Récupérez le code unique 
                                    et entrez-le sur notre site pour vous rapprocher encore un peu plus de votre récompense.
                                </>
                            )}
                            {stepNumber === 3 && (
                                <>
                                    <span role="img" aria-label="form">📝</span> Quelques clics pour compléter le formulaire et vous voilà prêt à tenter votre chance. 
                                    Pendant que vous savourez votre thé, laissez la magie opérer !
                                </>
                            )}
                            {stepNumber === 4 && (
                                <>
                                    <span role="img" aria-label="gift">🎁</span> Bravo ! Rendez-vous en boutique pour réclamer votre cadeau et plongez dans une nouvelle expérience de thé Tip Top, 
                                    offerte avec toute notre passion.
                                </>
                            )}
                        </p>
                    </div>
                </div>
            </div>
        ))}
    </div>
    <style jsx>{`
        /* Animation flottante */
        @keyframes floating {
            0% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-10px);
            }
            100% {
                transform: translateY(0px);
            }
        }

        .floating {
            animation: floating 3s ease-in-out infinite;
        }
    `}</style>
</section>

    </div>




                <Col className={`${stylesAux.topSectionRightSide} p-5 m-0`} md={12}>
                    <div className={`${stylesAux.homePageContentTopHeader}`}>
                        <h1 className={`mx-5`}>
                            Liste des Gains
                        </h1>
                        <div className={`${stylesAux.ticketsCardsMain}`}>

                            <div className={`${stylesAux.ticketsCardsDiv} mb-5 px-4`}>

                                <Row className={`${stylesAux.fullWidthElement}  mt-5 mb-5 w-100`}
                                     gutter={{xs: 8, sm: 16, md: 24, lg: 32}}>


                                    {loading &&
                                        <div className={`${stylesAux.loadingDashboardFullPage}`}>
                                            <Spin size="large"/>
                                        </div>
                                    }
                                    {!loading && (
                                        <>
                                            <Col key={"resultTikcets"}
                                                 className={`w-100 d-flex justify-content-between mt-3 px-4`} xs={24}
                                                 sm={24} md={24} lg={24} span={6}>


                                            </Col>
                                            {renderPrizes()}

                                            {totalPrizeCount == 0 && (
                                                <Col key={"noResultTikcets"}
                                                     className={`w-100 d-flex justify-content-center mt-3 px-4`} xs={24}
                                                     sm={24} md={24} lg={24} span={6}>
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
                </Col>


            </Row>
        </div>
    )
}

export default LandingPageTopSection
