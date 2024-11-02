import React, {useEffect, useState , useRef} from 'react';
import styles from "@/styles/pages/dashboards/storeAdminDashboard.module.css";
import {Button, Col, Divider, Modal, Row, Tag} from "antd";
import {getFinalDrawHistory, getGameConfig, realFinalDrawCall, testFinalDrawCall} from "@/app/api";
import LogoutService from "@/app/service/LogoutService";

import {HarmonyOSOutlined} from "@ant-design/icons";

import DecodeAnimation from "react-decode-animation";

interface DataType {
    startDate: string;
    time: string;
}

interface TimeRemaining {
    days: number,
    hours: number,
    minutes: number,
    seconds: number
}

interface DataUser {
    id: number;
    lastname: string;
    firstname: string;
    email: string;
    status: string;
    role: string;
    dateOfBirth: string;
    phone: string;
    gender: string;
    age: number;
}

interface FinalDrawDataType {
    history: {
        date: string;
        time: string;
    }
    winner: {
        id: number;
        lastname: string;
        firstname: string;
        email: string;
        phone: string;
        gender: string;
        age: number;
    }
}

function TirageAuSortBailiffTemplate() {
    const ref = useRef(null);

    const [gameStatus, setGameStatus] = useState<string>("");

    const {logoutAndRedirectAdminsUserToLoginPage} = LogoutService();

    const [loading, setLoading] = useState(false);

    const [gameConfig, setGameConfig] = useState<DataType>({
        startDate: "",
        time: ""
    });


    const [principalPeriodFinishAt, setPrincipalPeriodFinishAt] = useState<DataType>({
        startDate: "",
        time: ""
    });


    const [validationPeriodFinishAt, setValidationPeriodFinishAt] = useState<DataType>({
        startDate: "",
        time: ""
    });




    const [timeRemaining, setTimeRemaining] = useState<TimeRemaining>({
        days: 0,
        hours: 0,
        minutes: 0,
        seconds: 0
    });


    const [gameConfigOriginal, setGameConfigOriginal] = useState<DataType>({
        startDate: "",
        time: ""
    });

    const [participantsCount , setParticipantsCount] = useState<number>(0);
    function fetchGameConfig() {
        setLoading(true);
        getGameConfig().then((response) => {
            if (response) {
                setGameConfigOriginal({
                    startDate: response.gameConfig,
                    time: response.time
                })

                setPrincipalPeriodFinishAt({
                    startDate: response.principalPeriodFinishAt.date,
                    time: response.principalPeriodFinishAt.time
                });

                setValidationPeriodFinishAt({
                    startDate: response.validationPeriodFinishAt.date,
                    time: response.validationPeriodFinishAt.time
                });

                setGameStatus(response.gameStatus);

                const originalDate = response.gameConfig;
                const [day, month, year] = originalDate.split('/');


                const formatedDate = `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`;



                setGameConfig({
                    startDate: formatedDate,
                    time: response.time
                });

                const timeRemainingToStart = response.timeRemainingToStart;
                setTimeRemaining({
                    days: timeRemainingToStart.days,
                    hours: timeRemainingToStart.hours,
                    minutes: timeRemainingToStart.minutes,
                    seconds: timeRemainingToStart.seconds
                });

                setParticipantsCount(response.participantsCount);

            }
        }).catch((err) => {
            if (err.response) {
                if (err.response.status === 401) {
                    logoutAndRedirectAdminsUserToLoginPage();
                }
            } else {
                console.log(err.request);
            }
        })
        setLoading(false);
    }

    useEffect(() => {
        fetchGameConfig();
    }, []);

    const statusColors: Record<string, string> = {
        "A venir": "processing",
        "En cours": "success",
        "Terminé": "error",
    };


    const [classColorTag , setClassColorTag] = useState<string>("");
    useEffect(() => {
        setClassColorTag(statusColors[gameStatus] as any);
    }, [gameStatus]);


    const [modalVisible, setModalVisible] = useState(false);
    const [winner, setWinner] = useState<string>("AMMAR Amine");
    const [winnerUser , setWinnerUser] = useState<DataUser>({} as DataUser);

    function testFinalDraw() {
        setDecodeState("Reset");
        setFinalDrawDone(false);
        testFinalDrawCall().then((response) => {
            if (response) {
                console.log(response);
                setWinner(response.winner);
                setWinnerUser(response.user);
                setModalVisible(true);

            }
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

    const [decodeState, setDecodeState] = useState("Paused");
    const [finalDrawDone, setFinalDrawDone] = useState<boolean>(false);
    function testFinalDrawProcess() {
        setDecodeState("Reset");

        setFinalDrawDone(!finalDrawDone);

        if(finalDrawDone) {
            return;
        }

        setTimeout(() => {
            setDecodeState("Playing");
        }, 90);
    }

    function showClientDetails() {
        console.log(winnerUser);

        Modal.success({
            title: 'Détails du gagant',
            content: (
                <div>
                    <p>
                        <strong>Nom : </strong> {winnerUser.lastname}
                    </p>
                    <p>
                        <strong>Prénom : </strong> {winnerUser.firstname}
                    </p>
                    <p>
                        <strong>Email : </strong> {winnerUser.email}
                    </p>
                    <p>
                        <strong>Numéro de téléphone : </strong> {winnerUser.phone}
                    </p>
                    <p>
                        <strong>Genre : </strong> {winnerUser.gender}
                    </p>


                    <br/>

                    <strong>
                        <small>
                            Le tirage au sort final est une simulation, ces données ne seront pas enregistrées dans la base de données.
                        </small>
                    </strong>

                    <br/>


                </div>),

            onOk() {},
        });

    }

    function realFinalDraw() {
        setDecodeState("Reset");
        setFinalDrawDone(false);
        realFinalDrawCall().then((response) => {
            if (response) {
                console.log(response);
                setWinner(response.winner);
                setWinnerUser(response.user);
                setModalVisible(true);

            }
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

    const [finalDrawHistory , setFinalDrawHistory] = useState<{}>({});


    function fetchFinalDrawHistory() {
        setLoading(true);
        getFinalDrawHistory().then((response) => {
            if (response) {
                console.log(response);
                setFinalDrawHistory(response);
            }
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

    useEffect(() => {
        fetchFinalDrawHistory();
    }, []);

    useEffect(() => {
        console.log("finalDrawHistory : ",finalDrawHistory)
    }, [finalDrawHistory]);



    return (
        <div className={styles.homePageContent}>

            <div className={`${styles.homePageContentTopHeader}`}>
                <h1 className={`mx-3`}>
                    Tirage au sort final
                </h1>

                <div className={`${styles.ticketsCardsMain} mb-0 mt-5`}>
                    <div className={`${styles.ticketsCardsDiv} ${styles.correspandancesDiv} mb-5 px-4`}>
                        <div className={` w-100 ${styles.templatesPersoDiv}`}>

                            <h4 className={"mt-5"}>
                                Le participant gagnant du tirage au sort final sera affiché ci-dessous. ✅
                            </h4>

                            <ul>
                                <li>
                                    <span>
                                        Date de confirmation du tirage au sort final :
                                    </span>
                                </li>

                                <li>
                                    <span>
                                        #ID du gagnant :
                                    </span>
                                </li>


                                <li>
                                    <span>
                                        Nom complet du gagnant :
                                    </span>
                                </li>

                                <li>
                                    <span>
                                        Email du gagnant :
                                    </span>
                                </li>

                                <li>
                                    <span>
                                        Numéro de téléphone du gagnant :
                                    </span>
                                </li>


                                <li>
                                    <span>
                                        Genre du gagnant :
                                    </span>
                                </li>


                                <li>
                                    <span>
                                        Age du gagnant :
                                    </span>
                                </li>




                            </ul>


                        </div>

                    </div>

                </div>

                <div className={`${styles.ticketsCardsMain} mt-1`}>
                    <div className={`${styles.ticketsCardsDiv} ${styles.correspandancesDiv} mb-5 px-4`}>
                        <div className={` w-100 ${styles.templatesPersoDiv}`}>

                            {gameStatus !== "Terminé" && (
                                <>
                                    <h4 className={"mt-5"}>
                                        On vous propose de tester le tirage au sort final avant la fin de la période de
                                        validation.(simulation) 🚀
                                    </h4>
                                    <h5 className={"mt-5"}>
                                        Le tirage au sort final se déroulera à la fin de la période de validation du
                                        jeu.
                                    </h5>


                                    <ul>
                                        <li>
                                    <span>
                                A partir du {validationPeriodFinishAt.startDate} à {validationPeriodFinishAt.time}
                                    </span>
                                        </li>
                                        <li>
                                    <span>
                                        Nombre de clients participants : {participantsCount}
                                    </span>
                                        </li>

                                    </ul>


                                    <strong>
                                        Testez le tirage au sort final (simulation) 🚀 <small>
                                        Ce teste est une simulation du tirage au sort final, il ne sera pas enregistré
                                        dans la base de données. (possibilité de tester le tirage au sort final avant la
                                        fin de la période de validation)
                                    </small>
                                    </strong>
                                    <br/>
                                    <br/>


                                    <strong>
                                        Simulation de tirage au sort final :
                                    </strong>

                                    <small className={`mx-2`}>
                                        Cliquez sur le bouton ci-dessous pour tester le tirage au sort final.
                                    </small>

                                    <div className="d-flex justify-content-center my-4 align-items-center">
                                        <Button
                                            disabled={gameStatus === "Terminé"}
                                            onClick={() => {
                                                testFinalDraw();
                                            }} className={`w-100 ${styles.testDrawBtn}`} htmlType="submit">
                                            Tester le tirage au sort final <HarmonyOSOutlined className={`mx-2`}/>
                                        </Button>
                                    </div>


                                    <Divider/>


                                </>
                            )}

                            {/*  {gameStatus === "Terminé" && (
                                <>*/}
                            <h5 className={"mt-5"}>
                                Veuillez procéder au tirage au sort final en cliquant sur le bouton ci-dessous. 🚀
                            </h5>

                            <ul>
                                <li>
                                    <span>
                                        Nombre de clients participants : {participantsCount}
                                    </span>
                                </li>
                            </ul>


                            <strong>
                                Tirage au sort final :
                            </strong>

                            <small className={`mx-2`}>
                                Cliquez sur le bouton ci-dessous pour effectuer le tirage au sort final.
                            </small>

                            <div className="d-flex justify-content-center my-4 align-items-center">
                                <Button
                                    disabled={gameStatus !== "Terminé"}
                                    onClick={() => {
                                        realFinalDraw();
                                    }} className={`w-100 ${styles.testDrawBtn}`} htmlType="submit">
                                    Effectuer le tirage au sort final <HarmonyOSOutlined className={`mx-2`}/>
                                </Button>
                            </div>


                            <Divider/>
                            {/*   </>
                            )}*/}

                            <h5 className={"mt-5"}>

                                {gameStatus === "A venir" && (
                                    <>
                                        Début du jeu 🚀
                                    </>
                                )}

                                {gameStatus === "En cours" && (
                                    <>
                                        Date du jeu 🏁
                                    </>
                                )}

                                {gameStatus === "Validation" && (
                                    <>
                                        Date de la période de validation 🕣
                                    </>
                                )}

                                {gameStatus === "Terminé" && (
                                    <>
                                        Date de fin du jeu 🏁
                                    </>
                                )}


                                <Tag color={classColorTag} className="ms-3">{gameStatus}</Tag>
                                <br/>
                                {gameStatus === "En cours" && (
                                    <>
                                        <small>
                                            Du {gameConfigOriginal.startDate} à {gameConfigOriginal.time} jusqu'au {principalPeriodFinishAt.startDate} à {principalPeriodFinishAt.time}
                                        </small>
                                    </>
                                )}

                                {gameStatus === "A venir" && (
                                    <>
                                        <small>
                                            Du {gameConfigOriginal.startDate} à {gameConfigOriginal.time} jusqu'au {principalPeriodFinishAt.startDate} à {principalPeriodFinishAt.time}
                                        </small>
                                    </>
                                )}

                                {gameStatus === "Validation" && (
                                    <>
                                        <small>
                                            Du {principalPeriodFinishAt.startDate} à {principalPeriodFinishAt.time} jusqu'au {validationPeriodFinishAt.startDate} à {validationPeriodFinishAt.time}
                                        </small>
                                    </>
                                )}

                                {gameStatus === "Terminé" && (
                                    <>
                                        <small>
                                            Du {gameConfigOriginal.startDate} à {gameConfigOriginal.time} jusqu'au {principalPeriodFinishAt.startDate} à {principalPeriodFinishAt.time}
                                        </small>
                                    </>
                                )}


                            </h5>


                            <Divider/>
                            <strong className={`my-5 d-flex justify-content-start`}>
                                Vue d'ensemble
                            </strong>


                            <Row className={`w-100`} style={{position: 'relative'}}>

                                <Col span={12}
                                     className={"m-0 p-0 d-flex flex-column justify-content-start align-items-start pe-5"}>
                                    <h5>
                                        Phase initiale du jeu
                                    </h5>

                                    <p>
                                        La première phase du jeu se déroulera sur une période de 30 jours, débutant
                                        le <strong className={"fw-bold"}>
                                        {gameConfigOriginal.startDate} à {gameConfigOriginal.time}
                                    </strong> et se clôturant le <strong className={"fw-bold"}>
                                        {principalPeriodFinishAt.startDate} à {principalPeriodFinishAt.time}
                                    </strong>.
                                    </p>

                                    <p>
                                        Les participants auront ensuite une période additionnelle de 30 jours après la
                                        fermeture de la première phase pour visiter le site internet, tester leurs
                                        tickets, et réclamer leurs lots, que ce soit en magasin ou en ligne.
                                    </p>

                                </Col>

                                <Col span={12}
                                     className={"m-0 p-0 d-flex flex-column justify-content-start align-items-start pe-5"}>
                                    <h5>
                                        Phase de validation
                                    </h5>

                                    <p>
                                        La seconde phase du jeu débutera immédiatement après la première, couvrant les
                                        30 jours suivants. La date de début sera <strong className={"fw-bold"}>
                                        {principalPeriodFinishAt.startDate} à {principalPeriodFinishAt.time}
                                    </strong> et la clôture aura lieu le <strong className={"fw-bold"}>
                                        {validationPeriodFinishAt.startDate} à {validationPeriodFinishAt.time}
                                    </strong>.
                                    </p>

                                </Col>

                                <Col span={24}
                                     className={"m-0 p-0 d-flex flex-column justify-content-start align-items-start"}>
                                    <h5>
                                        {gameStatus === "A venir" && (
                                            <>
                                                Le jeu débute dans
                                            </>
                                        )}

                                        {gameStatus === "En cours" && (
                                            <>
                                                Fin du jeu dans
                                            </>
                                        )}

                                        {gameStatus === "Validation" && (
                                            <>
                                                Fin de la période de validation dans
                                            </>
                                        )}

                                        {gameStatus === "Terminé" && (
                                            <>
                                                Cloturé depuis
                                            </>
                                        )}

                                    </h5>
                                    <section className="timeContainer">
                                        <div className="wrapper">
                                            <div className="days">
                                                <h2 id="days">
                                                    {timeRemaining.days}
                                                </h2>
                                                Jours
                                            </div>
                                            <div className="hours">
                                                <h2 id="hours">
                                                    {timeRemaining.hours}
                                                </h2>
                                                Heures
                                            </div>
                                            <div className="minutes">
                                                <h2 id="minutes">
                                                    {timeRemaining.minutes}
                                                </h2>
                                                Minutes
                                            </div>
                                            <div className="seconds">
                                                <h2 id="seconds">
                                                    {timeRemaining.seconds}
                                                </h2>
                                                Secondes
                                            </div>
                                        </div>
                                    </section>
                                </Col>

                            </Row>


                        </div>

                    </div>

                </div>
            </div>

            <Modal
                className={``}
                title="Tirage au sort final"
                open={modalVisible}
                onCancel={() => setModalVisible(false)}
                footer={[
                    <Button key="ok" onClick={() => setModalVisible(false)}>
                        Fermer
                    </Button>
                ]}
            >

                <div className={`w-100 d-flex flex-column justify-content-center align-items-center`}>
                    <Button className={`w-100 ${styles.testDrawBtn}`} htmlType="submit"
                            onClick={() => {
                                testFinalDrawProcess();
                            }}
                    >

                        {
                            finalDrawDone && (
                                <span className={`ms-2`}>
                                    Réinitialiser le tirage au sort final
                                </span>
                            )
                        }

                        {
                            !finalDrawDone && (
                                <span className={`ms-2`}>
                                    Lancer le tirage au sort final
                                </span>
                            )
                        }

                        <HarmonyOSOutlined className={`mx-2`}/>
                    </Button>

                    <DecodeAnimation
                        ref={ref}
                        autoplay
                        text={winner}
                        interval={500}
                        state={decodeState as any}
                        className={`${styles.decodeAnimation}`}
                        customCharacters="#*!&@?%$"
                        onFinish={() => {
                            setDecodeState("Paused");
                            showClientDetails();
                        }}

                    />
                </div>

            </Modal>

        </div>


    );
}

export default TirageAuSortBailiffTemplate;