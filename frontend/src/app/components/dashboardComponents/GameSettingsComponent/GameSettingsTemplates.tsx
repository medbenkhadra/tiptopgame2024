import React, {useEffect, useState} from 'react';
import styles from "@/styles/pages/dashboards/storeAdminDashboard.module.css";
import LogoutService from "@/app/service/LogoutService";
import {Button, Col, Divider, Form, Input, Modal, Row, Tag} from "antd";
import {getGameConfig, updateGameConfig} from "@/app/api";


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

function GameSettingsTemplates() {



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

    const [lockedForm, setLockedForm] = useState<boolean>(true);

    const [gameStatus, setGameStatus] = useState<string>("");

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



    function onFinish() {
        console.log("finish")
        console.log(gameConfig)
        setLockedForm(true);


    }

    function updateStartDateGame() {
        Modal.confirm({
            title: 'Êtes-vous sûr de vouloir modifier la date de début du jeu ?',
            content: 'Cela peut affecter les joueurs et les tickets déjà enregistrés',
            okText: 'Oui',
            okType: 'danger',
            cancelText: 'Non',
            onOk() {
                updateGameConfig(gameConfig).then((response) => {
                    if (response) {
                        Modal.success({
                            title: 'Date de début du jeu modifiée avec succès',
                            content: 'La date de début du jeu a été modifiée avec succès',
                        });

                        setLockedForm(true);
                        fetchGameConfig();
                    }
                }).catch((err) => {
                    if (err.response) {
                        if (err.response.status === 401) {
                            logoutAndRedirectAdminsUserToLoginPage();
                        }
                    } else {
                        Modal.error({
                            title: 'Erreur lors de la modification de la date de début du jeu',
                            content: 'Une erreur s\'est produite lors de la modification de la date de début du jeu',
                        });
                    }
                });
            },
        });
    }

    function enableUpdate() {
        Modal.confirm({
            title: 'Êtes-vous sûr de vouloir modifier la date de début du jeu ?',
            content: 'Cela peut affecter les joueurs et les tickets déjà enregistrés',
            okText: 'Oui',
            okType: 'danger',
            cancelText: 'Non',
            onOk() {
                setLockedForm(false);
            },
        });
    }

    const statusColors: Record<string, string> = {
        "A venir": "processing",
        "En cours": "success",
        "Terminé": "error",
    };


    const [classColorTag , setClassColorTag] = useState<string>("");
    useEffect(() => {
        setClassColorTag(statusColors[gameStatus] as any);
    }, [gameStatus]);

        return (
        <div className={styles.homePageContent}>

            <div className={`${styles.homePageContentTopHeader}`}>
                <h1 className={`mx-3`}>
                    Paramètres de Jeu TipTop
                </h1>
                <div className={`${styles.ticketsCardsMain} mt-5`}>
                    <div className={`${styles.ticketsCardsDiv} ${styles.correspandancesDiv} mb-5 px-4`}>

                        <div className={` w-100 ${styles.templatesPersoDiv}`}>
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

                            <Row
                                className={`w-100`}
                            >
                                <>
                                    <Form
                                        name="userInfo"
                                        onFinish={onFinish}
                                        layout="vertical"
                                        key={gameConfig.startDate}
                                        className={`w-100`}
                                    >


                                        <strong className={`my-5 d-flex justify-content-start`}>
                                            Veuillez entrer la date de début du jeu
                                        </strong>

                                        <Row gutter={16} style={{
                                            width: '100%',
                                            display: 'flex',
                                            justifyContent: 'flex-start',
                                            alignItems: 'flex-end'
                                        }}>
                                            <Col span={6}>
                                                <Form.Item
                                                    initialValue={gameConfig.startDate}
                                                    style={{width: '100%', margin: 0, padding: 0}}
                                                    name="gameStartDate"
                                                    label="Date de début"
                                                    rules={[{
                                                        required: true,
                                                        message: 'Veuillez entrer la date de début !'
                                                    }]}
                                                >
                                                    <Input defaultValue={gameConfig.startDate}
                                                           value={gameConfig.startDate} type="date"
                                                           onChange={(e) => setGameConfig({
                                                               ...gameConfig,
                                                               startDate: e.target.value
                                                           })} disabled={lockedForm}/>
                                                </Form.Item>

                                            </Col>


                                            <Col span={3}>
                                                <Form.Item
                                                    initialValue={gameConfig.time}
                                                    style={{width: '100%', margin: 0, padding: 0}}
                                                    name="gameTime"
                                                    label="Heure de début"
                                                    rules={[{
                                                        required: true,
                                                        message: 'Veuillez entrer l\'heure de début !'
                                                    }]}
                                                >
                                                    <Input defaultValue={gameConfig.time}
                                                           value={gameConfig.time} type="time"
                                                           onChange={(e) => setGameConfig({
                                                               ...gameConfig,
                                                               time: e.target.value
                                                           })} disabled={lockedForm}/>

                                                </Form.Item>
                                            </Col>


                                            <Col span={6}>
                                                {lockedForm && <Button type="primary"
                                                                       onClick={() => enableUpdate()}>Modifier</Button>}
                                                {!lockedForm && <Button type="primary"
                                                                        onClick={() => updateStartDateGame()}>Enregistrer</Button>}
                                            </Col>

                                        </Row>


                                    </Form>


                                </>
                            </Row>


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
                                                Temps restant avant le début du jeu 🚀
                                            </>
                                        )}

                                        {gameStatus === "En cours" && (
                                            <>
                                                Temps restant avant la fin du jeu 🏁
                                            </>
                                        )}

                                        {gameStatus === "Validation" && (
                                            <>
                                                Temps restant avant la fin de la période de validation 🕣
                                            </>
                                        )}

                                        {gameStatus === "Terminé" && (
                                            <>
                                                Le jeu est terminé depuis ✅
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
                                    </h5>
                                    <section className="timeContainer">
                                        <div className="wrapper">
                                            <div className={`days ${gameStatus=="Terminé" && "red-bg"}`}>
                                                <h2 id="days">
                                                    {timeRemaining.days}
                                                </h2>
                                                Jours
                                            </div>
                                            <div className={`hours ${gameStatus=="Terminé" && "red-bg"}`}>
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


        </div>
        );
}

export default GameSettingsTemplates;