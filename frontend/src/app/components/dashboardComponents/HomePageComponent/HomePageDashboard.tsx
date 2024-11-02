import React, {useEffect, useState} from 'react';
import styles from "@/styles/pages/dashboards/storeAdminDashboard.module.css";
import TicketImage from "@/assets/images/ticket.png";
import CodeScanner from "@/assets/images/scan.png";
import RemainsTickes from "@/assets/images/remainsTickes.png";
import ClientsImg from "@/assets/images/clients.png";
import UsersImg from "@/assets/images/users.png";
import RouletteImg from "@/assets/images/roulette.png";
import LoyaltyCardImg from "@/assets/images/loyaltyCard.png";
import {Col, ConfigProvider, DatePicker, Radio, Row, Spin} from 'antd';
import Image from 'next/image';
import CityStatsChart from "@/app/components/dashboardComponents/HomePageComponent/components/CityStatsChart";
import 
    PrizesChartDoughunt
 from "@/app/components/dashboardComponents/HomePageComponent/components/PrizesChartDoughnut";
const AnimatedNumbers = dynamic(() => import('react-animated-numbers'), { ssr: false });

import CalendarImg from "@/assets/images/calendar.png";
import PriceImg from "@/assets/images/price.png";
import PlayImg from "@/assets/images/play.png";
import StoresList from "@/app/components/dashboardComponents/HomePageComponent/components/StoresListHomePage";
import locale from "antd/locale/fr_FR";
import dayjs from "dayjs";
import 
    PrizesStatsWithAgeChart
 from "@/app/components/dashboardComponents/HomePageComponent/components/PrizesStatsWithAgeChart";
import 
    PrizesStatsWithSexChart
 from "@/app/components/dashboardComponents/HomePageComponent/components/PrizesStatsWithSexChart";
import 
    GamePlayedStatsChart
 from "@/app/components/dashboardComponents/HomePageComponent/components/GamePlayedStatsChart";
import 
    TopTeenParticipants
 from "@/app/components/dashboardComponents/HomePageComponent/components/TopTeenParticipants";
import 
    PrizesWinStatsByStore
 from "@/app/components/dashboardComponents/HomePageComponent/components/PrizesWinStatsByStore";
import {getAdminDashboardCardsCounters, getClientDashboardCardsCounters, getDashboardStatsData} from "@/app/api";
import PrizesStatsByStatusesChart from './components/PrizesStatsByStatusesChart';
import GameStatusesTendanceStatsChart from './components/GameStatusesTendanceStatsChart';
import PrizeStatsByGenderByAgeChart from './components/PrizeStatsByGenderByAgeChart';
import PrizesCostTendance from './components/PrizesCostTendance';
import GiftsImg from "@/assets/images/surprisePlus.png";
import TiptopEmployeeImg from "@/assets/images/tiptopEmployee.png";
import DashboardSpinnigLoader from "@/app/components/widgets/DashboardSpinnigLoader";
import SpinnigLoader from "@/app/components/widgets/SpinnigLoader";
import dynamic from "next/dynamic";

const dateFormat = 'DD/MM/YYYY';

interface SeachParams {
    startDate?: string| null;
    endDate?: string| null;
    storeId?: string| null;
    selectedPeriod?: string;
}

const search: SeachParams = {
    startDate: '01/01/2022',
    endDate: '30/12/2024',
    storeId: '',
    selectedPeriod: '',
};

function HomePage() {
    const [selectedStoreId, setSelectedStoreId] = useState<string>('');
    const { RangePicker } = DatePicker;
    const [loading, setLoading] = useState<boolean>(true);

    const [searchForm, setSearchForm] = useState<SeachParams>(search);


    const handleDateChange: any = (date : any, dateString :any)  => {


        setSearchForm((prevFormData) => ({
            ...prevFormData,
            startDate: dateString[0],
            endDate: dateString[1],
            selectedPeriod: 'xxx',
        }));
    };

    const handleStoreChange = (value: string) => {
        setSelectedStoreId(value);
        setSearchForm((prevFormData) => ({
            ...prevFormData,
            storeId: value,
        }));
    };

    const [userRole, setUserRole] = useState<string>('');
    const [token, setToken] = useState<string>('');
    const [adminDashboardCardsCounters, setAdminDashboardCardsCounters] = useState<any>({});
    useEffect(() => {
        const tokenAux = localStorage.getItem('loggedInUserToken');
        const userRoleAux = localStorage.getItem('loggedInUserRole');
        setToken(tokenAux as string);
        setUserRole(userRoleAux as string);
    }, []);

    useEffect(() => {
        setSecondLoading(true);
        const userRoleAux = localStorage.getItem('loggedInUserRole');
        if (userRoleAux == "ROLE_ADMIN" || userRoleAux == "ROLE_STOREMANAGER" || userRoleAux == "ROLE_EMPLOYEE") {
            getAdminDashboardCardsCounters(searchForm).then((res) => {
                setSecondLoading(false);
                setAdminDashboardCardsCounters(res.counters);
            }).catch((err) => {
                setSecondLoading(false);
                console.log(err);
            });
        }

    }, [searchForm]);


    const [statsData, setStatsData] = useState<any>({});
    const [secondLoading, setSecondLoading] = useState<boolean>(false);
    useEffect(() => {
       dashboardStatsCall().then(r => console.log(r));
        setSecondLoading(true);
    }, [searchForm]);

    async function dashboardStatsCall() {
        await getDashboardStatsData(searchForm).then((res) => {
            setStatsData(res);
            setSecondLoading(false);
            console.log(res);
        }).catch((err) => {
            setSecondLoading(false);
            console.log(err);
        });
    }

    useEffect(() => {
        if (statsData && Object.keys(statsData).length !== 0 && adminDashboardCardsCounters && Object.keys(adminDashboardCardsCounters).length !== 0) {
            setLoading(false);
        }
    }, [statsData , adminDashboardCardsCounters]);


    const [gainByPrizeData, setGainByPrizeData] = useState<any>([]);
    const [gainByPrizeChartToggle, setGainByPrizeChartToggle] = useState(0);
    useEffect(() => {
        if (statsData && Object.keys(statsData).length !== 0) {
            let dataAux = statsData["stats"]["gainByPrize"];
            let finalData:any[] = [];
            Object.entries(dataAux).map(([key, value]) => {
                let data = {
                    "label": key,
                    "value": value,
                };
                finalData.push(data);
            });
            setGainByPrizeChartToggle(gainByPrizeChartToggle + 1);
            setGainByPrizeData(finalData);
        }
    }, [statsData]);

    const [gainByGenderData, setGainByGenderData] = useState<any>([]);
    useEffect(() => {
        if (statsData && Object.keys(statsData).length !== 0) {
            let dataAux = statsData["stats"]["gainByGender"];
            let finalData:any[] = [];
            Object.entries(dataAux).map(([key, value]) => {
                let data = {
                    "label": key,
                    "value": value,
                };
                finalData.push(data);
            });
            setGainByGenderData(finalData);
        }
    }, [statsData]);

    const [gainByCityData, setGainByCityData] = useState<any>([]);
    useEffect(() => {
        if (statsData && Object.keys(statsData).length !== 0) {
            let dataAux = statsData["stats"]["gainByCity"];
            let finalData:any[] = [];
            Object.entries(dataAux).map(([key, value]) => {
                let data = {
                    "label": key,
                    "value": value,
                };
                finalData.push(data);
            });
            setGainByCityData(finalData);
        }
    }, [statsData]);


    const [gainByAgeData, setGainByAgeData] = useState<any>([]);
    useEffect(() => {
        if (statsData && Object.keys(statsData).length !== 0) {
            let dataAux = statsData["stats"]["gainByAge"];
            let finalData:any[] = [];
            Object.entries(dataAux).map(([key, value]) => {
                let data = {
                    "label": key,
                    "value": value,
                };
                finalData.push(data);
            });
            setGainByAgeData(finalData);
        }
    }, [statsData]);


    const [gainByStoresData, setGainByStoresData] = useState<any>([]);
    useEffect(() => {
        if (statsData && Object.keys(statsData).length !== 0) {
            let dataAux = statsData["stats"]["gainByStores"];
            let finalData:any[] = [];
            Object.entries(dataAux).map(([key, value]) => {
                let data = {
                    "label": key,
                    "value": value,
                };
                finalData.push(data);
            });
            setGainByStoresData(finalData);
        }
    }, [statsData]);


    const [topGainTableData, setTopGainTableData] = useState<any>([]);
    useEffect(() => {
        if (statsData && Object.keys(statsData).length !== 0) {
            let dataAux = statsData["topGain"]
            let finalData:any[] = [];
            Object.entries(dataAux).map(([key, value]) => {
                let data = {
                    "label": key,
                    "value": value,
                };
                finalData.push(data);
            });


            setTopGainTableData(finalData);
        }
    }, [statsData]);


    const [gainTendanceData, setGainTendanceData] = useState<any>([]);
    useEffect(() => {
        if (statsData && Object.keys(statsData).length !== 0) {
            let dataAux = statsData["stats"]["participationTendance"];
            let finalData:any[] = [];
            Object.entries(dataAux).map(([key, value]) => {
                let data = {
                    "label": key,
                    "value": value,
                };
                finalData.push(data);
            });
            setGainTendanceData(finalData);
        }
    }, [statsData]);

    const [ticketsByStatuses, setTicketsByStatuses] = useState<any>([]);
    useEffect(() => {
        if (statsData && Object.keys(statsData).length !== 0) {
            let dataAux = statsData["stats"]["ticketsByStatuses"];
            let finalData:any[] = [];
            Object.entries(dataAux).map(([key, value]) => {
                let data = {
                    "label": key,
                    "value": value,
                };
                finalData.push(data);
            });
            setTicketsByStatuses(finalData);
        }
    }, [statsData]);

    const [gainByGenderByAge, setGainByGenderByAge] = useState<any>([]);

    useEffect(() => {
        if (statsData && Object.keys(statsData).length !== 0) {
            let dataAux = statsData["stats"]["gainByGenderByAge"];
            console.log(dataAux , "dataAux");
            let finalData:any[] = [];
            Object.entries(dataAux).map(([key, value]) => {
                let data = {
                    "label": key,
                    "value": value,
                };
                finalData.push(data);
            });
            setGainByGenderByAge(finalData);
        }
    }, [statsData]);


    const [playGameTendanceData, setPlayGameTendanceData] = useState<any>([]);
    useEffect(() => {
        if (statsData && Object.keys(statsData).length !== 0) {
            let dataAux = statsData["stats"]["playGameTendance"];
            let finalData:any[] = [];
            Object.entries(dataAux).map(([key, value]) => {
                let data = {
                    "label": key,
                    "value": value,
                };
                finalData.push(data);
            });
            setPlayGameTendanceData(finalData);
        }
    }, [statsData]);



    const [prizesCostTendanceData, setPrizesCostTendanceData] = useState<any>([]);
    useEffect(() => {
        if (statsData && Object.keys(statsData).length !== 0) {
            let dataAux = statsData["stats"]["prizesCostTendance"];
            console.log(dataAux , "dataAux");
            let finalData:any[] = [];
            Object.entries(dataAux).map(([key, value]) => {
                let data = {
                    "label": key,
                    "value": value,
                };
                finalData.push(data);
            });
            setPrizesCostTendanceData(finalData);
        }
    }, [statsData]);


    const [clientCounter, setClientCounter] = useState<any>(null);
    useEffect(() => {
        const userRoleAux = localStorage.getItem('loggedInUserRole');
        if (userRoleAux == "ROLE_CLIENT" ) {
            getClientDashboardCounters();
        }
    }, []);
    function getClientDashboardCounters() {
        getClientDashboardCardsCounters().then((res) => {
            console.log(res.counters);
            setClientCounter(res.counters);
        }).catch((err) => {
            console.log(err);
        });
    }

    useEffect(() => {
        if (clientCounter && Object.keys(clientCounter).length !== 0 && statsData && Object.keys(statsData).length !== 0) {
            setLoading(false);
        }
    }, [clientCounter , statsData]);




    const handleRadioChange = (e:any) => {


        let today = dayjs();
        let startDate = dayjs();
        let endDate = dayjs();

        if (e.target.value === 'day') {
            startDate = today;
            endDate = today;
        }
        else if (e.target.value === '3days') {
            startDate = today.subtract(3, 'day');
            endDate = today;
        }
        else if (e.target.value === 'week') {
            startDate = today.subtract(7, 'day');
            endDate = today;
        } else if (e.target.value === 'month') {
            startDate = today.subtract(30, 'day');
            endDate = today;
        }else if (e.target.value === '2weeks') {
            startDate = today.subtract(14, 'day');
            endDate = today;
        }else if (e.target.value === '3month') {
            startDate = today.subtract(90, 'day');
            endDate = today;
        }else if (e.target.value === '') {
            startDate = today.subtract(365, 'day');
            endDate = today;
        }

        setSearchForm({
            ...searchForm,
            startDate: startDate.format(dateFormat),
            endDate: endDate.format(dateFormat),
            selectedPeriod: e.target.value,
        });



    };


    return (
       <>
           {loading && (
               <>
                <DashboardSpinnigLoader></DashboardSpinnigLoader>
               </>
           )}

           {!loading && (
               <>
                   <div className={styles.homePageContent}>

                       <div className={`${styles.homePageContentTopHeader}`}>
                           <h1 className={`mx-3`}>Tableau de bord </h1>
                           <div className={`${styles.homePageAdminCardsDiv}`}>

                               <Row className={`${styles.fullWidthElement} w-100 d-flex justify-content-center d-flex align-items-start`} gutter={{ xs: 8, sm: 16, md: 24, lg: 32 }} >

                                   <Col className={`w-100 p-0 m-0 ${styles.statsTopHeadetFilterDiv}`} sm={24} md={12} lg={12} span={12}>
                                   <Row className={`${styles.fullWidthElement} w-100 d-flex justify-content-center `} gutter={{ xs: 8, sm: 16, md: 24, lg: 32 }} >
                                        <Col className={`w-100 p-0 m-0 ${styles.statsTopHeadetFilterDiv} mt-2`} sm={24} md={24} lg={24} span={6}>
                                            <div className={`${styles.headetFilterDiv}`}>
                                                {userRole === "ROLE_ADMIN" && (
                                                    <>
                                                        <StoresList globalSelectedStoreId={selectedStoreId} onSelectStore={handleStoreChange}></StoresList>
                                                    </>
                                                )}
                                                <ConfigProvider locale={locale}>
                                                    <RangePicker
                                                        className={`${styles.datePickerDashboardHomePage}`}
                                                        onChange={(date:any , dateString:any )=>{
                                                            handleDateChange(date  , dateString)
                                                        }}
                                                        value={[dayjs(searchForm.startDate, dateFormat), dayjs(searchForm.endDate, dateFormat)]}
                                                        placeholder={['Date de début', 'Date de fin']}
                                                        format={dateFormat}
                                                        cellRender={(current:any) => {
                                                            const style: React.CSSProperties = {};
                                                            if (current.date() === 1) {
                                                                style.border = '1px solid #1677ff';
                                                                style.borderRadius = '50%';
                                                            }
                                                            return (
                                                                <div className="ant-picker-cell-inner" style={style}>
                                                                    {current.date()}
                                                                </div>
                                                            );
                                                        }}
                                                    />
                                                </ConfigProvider>
                                            </div>
                                        </Col>
                                        <Col className={`w-100 d-flex justify-content-start ${styles.periodsSelector}`} sm={24} md={24} lg={24} span={24}>
                                            <Radio.Group className={`${styles.periodsSelectorRadioGroup}`} onChange={handleRadioChange} value={searchForm.selectedPeriod}>
                                                <Radio className={`${styles.periodsSelectorRadio} periodsSelectorInput`} value="day">
                                                    1 Jour
                                                </Radio>

                                                <Radio className={`${styles.periodsSelectorRadio} periodsSelectorInput`} value="3days">
                                                    3 Jours
                                                </Radio>

                                                <Radio className={`${styles.periodsSelectorRadio} periodsSelectorInput`} value="week">
                                                    7 Jours
                                                </Radio>
                                                <Radio className={`${styles.periodsSelectorRadio} periodsSelectorInput`} value="2weeks">
                                                    14 Jours
                                                </Radio>
                                                <Radio className={`${styles.periodsSelectorRadio} periodsSelectorInput `} value="month">
                                                    30 Jours
                                                </Radio>
                                                <Radio className={`${styles.periodsSelectorRadio} periodsSelectorInput`} value="3month">
                                                    90 Jours
                                                </Radio>
                                                <Radio className={`${styles.periodsSelectorRadio} periodsSelectorInput `} value="">
                                                    1 an
                                                </Radio>

                                            </Radio.Group>
                                        </Col>


                                       <Col className={`w-100 d-flex justify-content-start mt-5`} sm={24} md={24} lg={24} span={24}>
                                           {secondLoading && (
                                               <SpinnigLoader></SpinnigLoader>
                                           )}
                                       </Col>
                                    </Row>
                                   </Col>



                                   {userRole === "ROLE_ADMIN" && (
                                       <Col className={`w-100 p-0 m-0 ${styles.statsTopHeadetFilterDiv}`} sm={24} md={12} lg={12} span={12}>
                                           <Row className={`${styles.fullWidthElement} w-100 d-flex justify-content-center d-flex `} gutter={{ xs: 8, sm: 16, md: 24, lg: 32 }} >
                                           <Col className={`w-100 m-0 p-0 ${styles.periodsSelector}`} sm={24} md={24} lg={24} span={24}>
                                           <div className={`${styles.homePageContentTopHeader}`}>
                                               <div className={`${styles.homePageAdminCardsDiv}`}>

                                                   <Row className={`${styles.fullWidthElement} w-100`} gutter={{ xs: 8, sm: 16, md: 24, lg: 32 }} >
                                                       <Col className={`w-100 d-flex`} sm={24} md={12} lg={8} span={6}>
                                                           <div className={`${styles.topCardElement} ${styles.minHeightCard}`}>

                                                               <div className={`${styles.topCardElementText}`}>
                                                                   <div className={`${styles.topCardElementIcon}`}>
                                                                       <Image src={RemainsTickes}  alt={"tickets"}></Image>
                                                                   </div>
                                                                   <div className={`${styles.counter}`}>
                                                                       <AnimatedNumbers
                                                                           includeComma
                                                                           className={styles.container}
                                                                           transitions={(index) => ({
                                                                               type: "spring",
                                                                               duration: index + 0.9,
                                                                           })}
                                                                           animateToNumber={adminDashboardCardsCounters["tickets"]}
                                                                       />
                                                                   </div>

                                                                   <div className={`${styles.cardTitle}`}>Total Des Lots</div>
                                                               </div>
                                                           </div>
                                                       </Col>
                                                       <Col className={`w-100 d-flex`} sm={24} md={12} lg={8} span={6}>
                                                           <div className={`${styles.topCardElement} ${styles.minHeightCard}`}>

                                                               <div className={`${styles.topCardElementText}`}>
                                                                   <div className={`${styles.topCardElementIcon}`}>
                                                                       <Image src={CodeScanner}  alt={"tickets"}></Image>
                                                                   </div>
                                                                   <div className={`${styles.counter}`}>
                                                                       <AnimatedNumbers
                                                                           includeComma
                                                                           className={styles.container}
                                                                           transitions={(index) => ({
                                                                               type: "spring",
                                                                               duration: index + 0.9,
                                                                           })}
                                                                           animateToNumber={adminDashboardCardsCounters["printedTickets"]}
                                                                       />
                                                                   </div>

                                                                   <div className={`${styles.cardTitle}`}>Bons Imprimés</div>
                                                               </div>
                                                           </div>
                                                       </Col>
                                                       <Col className={`w-100 d-flex`} sm={24} md={12} lg={8} span={6}>
                                                           <div className={`${styles.topCardElement} ${styles.minHeightCard}`}>

                                                               <div className={`${styles.topCardElementText}`}>
                                                                   <div className={`${styles.topCardElementIcon}`}>
                                                                       <Image src={TicketImage}  alt={"tickets"}></Image>
                                                                   </div>
                                                                   <div className={`${styles.counter}`}>
                                                                       {
                                                                           userRole === "ROLE_ADMIN" && (
                                                                               <AnimatedNumbers
                                                                                   includeComma
                                                                                   className={styles.container}
                                                                                   transitions={(index) => ({
                                                                                       type: "spring",
                                                                                       duration: index + 0.9,
                                                                                   })}
                                                                                   animateToNumber={adminDashboardCardsCounters["ticketStock"]}
                                                                               />
                                                                           )
                                                                       }

                                                                       {
                                                                           userRole != "ROLE_ADMIN" && (
                                                                               <AnimatedNumbers
                                                                                   includeComma
                                                                                   className={styles.container}
                                                                                   transitions={(index) => ({
                                                                                       type: "spring",
                                                                                       duration: index + 0.9,
                                                                                   })}
                                                                                   animateToNumber={adminDashboardCardsCounters["confirmedTickets"]}
                                                                               />
                                                                           )
                                                                       }
                                                                   </div>

                                                                   <div className={`${styles.cardTitle}`}>
                                                                       {
                                                                           userRole === "ROLE_ADMIN" && (
                                                                               <>
                                                                                   Lots Restants
                                                                               </>
                                                                           )
                                                                       }

                                                                       {
                                                                           userRole != "ROLE_ADMIN" && (
                                                                               <>
                                                                                   Lots Confirmés
                                                                               </>
                                                                           )
                                                                       }
                                                                   </div>
                                                               </div>
                                                           </div>
                                                       </Col>
                                                       <Col className={`w-100 d-flex`} sm={24} md={12} lg={8} span={6}>
                                                           <div className={`${styles.topCardElement} ${styles.minHeightCard}`}>

                                                               <div className={`${styles.topCardElementText}`}>
                                                                   <div className={`${styles.topCardElementIcon}`}>
                                                                       <Image src={UsersImg}  alt={"tickets"}></Image>
                                                                   </div>
                                                                   <div className={`${styles.counter}`}>
                                                                       <AnimatedNumbers
                                                                           includeComma
                                                                           className={styles.container}
                                                                           transitions={(index) => ({
                                                                               type: "spring",
                                                                               duration: index + 0.9,
                                                                           })}
                                                                           animateToNumber={adminDashboardCardsCounters["clients"]}
                                                                       />
                                                                   </div>

                                                                   <div className={`${styles.cardTitle}`}>
                                                                       {
                                                                           userRole === "ROLE_ADMIN" && (
                                                                               <>
                                                                                   Clients Inscrits
                                                                               </>
                                                                           )
                                                                       }

                                                                       {
                                                                           userRole != "ROLE_ADMIN" && (
                                                                               <>
                                                                                   Clients associés
                                                                               </>
                                                                           )
                                                                       }
                                                                   </div>
                                                               </div>
                                                           </div>
                                                       </Col>
                                                       <Col className={`w-100 d-flex`} sm={24} md={12} lg={8} span={6}>
                                                           <div className={`${styles.topCardElement} ${styles.minHeightCard}`}>
                                                               <div className={`${styles.topCardElementText}`}>
                                                                   <div className={`${styles.topCardElementIcon}`}>
                                                                       <Image src={ClientsImg}  alt={"tickets"}></Image>
                                                                   </div>
                                                                   <div className={`${styles.counter}`}>
                                                                       <AnimatedNumbers
                                                                           includeComma
                                                                           className={styles.container}
                                                                           transitions={(index) => ({
                                                                               type: "spring",
                                                                               duration: index + 0.9,
                                                                           })}
                                                                           animateToNumber={adminDashboardCardsCounters["participants"]}
                                                                       />

                                                                   </div>
                                                                   <div className={`${styles.cardTitle}`}>
                                                                       {
                                                                           userRole === "ROLE_ADMIN" && (
                                                                               <>
                                                                                   Participants
                                                                               </>
                                                                           )
                                                                       }

                                                                       {
                                                                           userRole != "ROLE_ADMIN" && (
                                                                               <>
                                                                                   Participants associés
                                                                               </>
                                                                           )
                                                                       }
                                                                   </div>

                                                               </div>
                                                           </div>
                                                       </Col>
                                                       <Col className={`w-100 d-flex`} sm={24} md={12} lg={8} span={6}>
                                                           <div className={`${styles.topCardElement} ${styles.minHeightCard}`}>
                                                               <div className={`${styles.topCardElementText}`}>
                                                                   <div className={`${styles.topCardElementIcon}`}>
                                                                       <Image src={RouletteImg}  alt={"tickets"}></Image>
                                                                   </div>
                                                                   <div className={`${styles.counter}`}>
                                                                       <AnimatedNumbers
                                                                           includeComma
                                                                           className={styles.container}
                                                                           transitions={(index) => ({
                                                                               type: "spring",
                                                                               duration: index + 0.9,
                                                                           })}
                                                                           animateToNumber={adminDashboardCardsCounters["playedTicket"]}
                                                                       />
                                                                   </div>
                                                                   <div className={`${styles.cardTitle}`}>
                                                                       {
                                                                           userRole === "ROLE_ADMIN" && (
                                                                               <>
                                                                                   Tickets Joués
                                                                               </>
                                                                           )
                                                                       }

                                                                       {
                                                                           userRole != "ROLE_ADMIN" && (
                                                                               <>
                                                                                   Tours de roue joués
                                                                               </>
                                                                           )
                                                                       }
                                                                   </div>
                                                               </div>
                                                           </div>
                                                       </Col>

                                                   </Row>

                                               </div>
                                           </div>
                                           </Col>
                                           </Row>
                                       </Col>
                                   )}


                                   {userRole === "ROLE_CLIENT" && (
                                       <Col className={`w-100 pt-0 mt-0 ${styles.statsTopHeadetFilterDiv}`} sm={24} md={24} lg={24} span={6}>
                                           <Row className={`${styles.fullWidthElement} w-100 d-flex justify-content-center d-flex `} gutter={{ xs: 8, sm: 16, md: 24, lg: 32 }} >

                                               <Col className={`w-100 ${styles.periodsSelector}`} sm={24} md={24} lg={24} span={24}>
                                                   <div className={`${styles.homePageContentTopHeader}`}>

                                                       <div className={`${styles.homePageAdminCardsDiv}`}>

                                                           <Row className={`${styles.fullWidthElement} w-100 d-flex justify-content-center`} gutter={{ xs: 8, sm: 16, md: 24, lg: 32 }} >


                                                               <Col className={`w-100 d-flex`} sm={24} md={12} lg={6} span={6}>

                                                                   <div className={`${styles.clientTopCardDashboard} ${styles.topCardElementAux}`}>
                                                                       <div className={`${styles.topCardElementText}`}>
                                                                           <div className={`${styles.topCardElementIcon}`}>
                                                                               <Image src={LoyaltyCardImg}  alt={"loyaltyCard"}></Image>
                                                                           </div>
                                                                           <div className={`${styles.counter}`}>
                                                                               <AnimatedNumbers
                                                                                   includeComma
                                                                                   className={styles.container}
                                                                                   transitions={(index) => ({
                                                                                       type: "spring",
                                                                                       duration: index + 0.9,
                                                                                   })}
                                                                                   animateToNumber={clientCounter?.["loyaltyPoints"]}
                                                                               />

                                                                           </div>
                                                                           <div className={`${styles.cardTitle}`}>
                                                                                 Points de fidélité
                                                                           </div>
                                                                       </div>
                                                                   </div>
                                                               </Col>
                                                               <Col className={`w-100 d-flex`} sm={24} md={12} lg={6} span={6}>

                                                                   <div className={`${styles.clientTopCardDashboard} ${styles.topCardElementAux}`}>
                                                                       <div className={`${styles.topCardElementText}`}>
                                                                           <div className={`${styles.topCardElementIcon}`}>
                                                                               <Image src={RouletteImg}  alt={"tickets"}></Image>
                                                                           </div>
                                                                           <div className={`${styles.counter}`}>
                                                                               <AnimatedNumbers
                                                                                   includeComma
                                                                                   className={styles.container}
                                                                                   transitions={(index) => ({
                                                                                       type: "spring",
                                                                                       duration: index + 0.9,
                                                                                   })}
                                                                                   animateToNumber={clientCounter?.["playedTickets"]}
                                                                               />

                                                                           </div>
                                                                           <div className={`${styles.cardTitle}`}>
                                                                               Tours Joués
                                                                           </div>
                                                                       </div>

                                                                   </div>
                                                               </Col>
                                                               <Col className={`w-100 d-flex`} sm={24} md={12} lg={6} span={6}>

                                                                   <div className={`${styles.clientTopCardDashboard} ${styles.topCardElementAux}`}>
                                                                       <div className={`${styles.topCardElementText}`}>
                                                                           <div className={`${styles.topCardElementIcon}`}>
                                                                               <Image src={GiftsImg}  alt={"GiftsImg"}></Image>
                                                                           </div>
                                                                           <div className={`${styles.counter}`}>
                                                                               <AnimatedNumbers
                                                                                   includeComma
                                                                                   className={styles.container}
                                                                                   transitions={(index) => ({
                                                                                       type: "spring",
                                                                                       duration: index + 0.9,
                                                                                   })}
                                                                                   animateToNumber={clientCounter?.["confirmedTickets"]}
                                                                               />
                                                                           </div>
                                                                           <div className={`${styles.cardTitle}`}>
                                                                               Cadeaux Réclamés
                                                                           </div>
                                                                       </div>

                                                                   </div>

                                                               </Col>

                                                               <Col className={`w-100 d-flex`} sm={24} md={12} lg={6} span={6}>

                                                                   <div className={`${styles.clientTopCardDashboard} ${styles.topCardElementAux}`}>
                                                                       <div className={`${styles.topCardElementText}`}>
                                                                           <div className={`${styles.topCardElementIcon}`}>
                                                                               <Image src={TiptopEmployeeImg}  alt={"TiptopEmployeeImg"}></Image>
                                                                           </div>
                                                                           <div className={`${styles.counter}`}>
                                                                               <AnimatedNumbers
                                                                                   includeComma
                                                                                   className={styles.container}
                                                                                   transitions={(index) => ({
                                                                                       type: "spring",
                                                                                       duration: index + 0.9,
                                                                                   })}
                                                                                   animateToNumber={clientCounter?.["pendingTickets"]}
                                                                               />
                                                                           </div>
                                                                           <div className={`${styles.cardTitle}`}>
                                                                               En attente de validation
                                                                           </div>
                                                                       </div>

                                                                   </div>

                                                               </Col>


                                                           </Row>

                                                       </div>
                                                   </div>
                                               </Col>
                                           </Row>
                                       </Col>
                                   )}



                               </Row>


                           </div>
                       </div>





                       <div className={`${styles.homePageContentStats}`}>





                           <h2 className={`mx-3`}>Vue d'Ensemble du Jeu (Statistiques)</h2>
                                   <div className={`${styles.homePageAdminStatsDiv}`}>

                                       <Row className={`${styles.fullWidthElement} w-100`} gutter={{ xs: 8, sm: 16, md: 24, lg: 32 }} >
                                           <div className={`${styles.boxShadowDiv}`}>
                                               <Row className={`${styles.fullWidthElement} w-100`} gutter={{ xs: 8, sm: 16, md: 24, lg: 32 }} >


                                                   <Col className={`w-100 ${styles.statsCharts}`} sm={24} md={24} lg={12} span={6}>
                                                       <h5 className={'mb-0'}>
                                                           Résultat De Recherche
                                                       </h5>
                                                       <div className={`${styles.fullWidthElement}`}>
                                                           <Row className={`${styles.fullWidthElement} w-100`} gutter={{ xs: 8, sm: 16, md: 24, lg: 32 }} >
                                                               <Col className={`w-100 d-flex`} sm={24} md={24} lg={12} span={12}>
                                                                   <div className={`${styles.topCardElement}`}>
                                                                       <div className={`${styles.topCardElementIconBadge}`}>
                                                                           <Image src={CalendarImg}  alt={"Dates"}></Image>
                                                                       </div>
                                                                       <div className={`${styles.topCardElementText} ${styles.topCardElementTextDates}`}>
                                                                           <div className={`${styles.topCardElementTextDatesTitle}`}>Date de début</div>
                                                                           <div className={`${styles.topCardElementTextDatesTitle}`}>
                                                                               {statsData["startDate"]}
                                                                           </div>
                                                                       </div>
                                                                   </div>
                                                               </Col>
                                                               <Col className={`w-100 d-flex`} sm={24} md={24} lg={12} span={12}>
                                                                   <div className={`${styles.topCardElement}`}>
                                                                       <div className={`${styles.topCardElementIconBadge}`}>
                                                                           <Image src={CalendarImg}  alt={"Dates"}></Image>
                                                                       </div>
                                                                       <div className={`${styles.topCardElementText} ${styles.topCardElementTextDates}`}>
                                                                           <div className={`${styles.topCardElementTextDatesTitle}`}>Date de fin</div>
                                                                           <div className={`${styles.topCardElementTextDatesTitle}`}>
                                                                               {statsData["endDate"]}
                                                                           </div>
                                                                       </div>
                                                                   </div>
                                                               </Col>
                                                           </Row>
                                                           <Row className={`${styles.fullWidthElement} w-100`} gutter={{ xs: 8, sm: 16, md: 24, lg: 32 }} >
                                                               <Col className={`w-100 d-flex`} sm={24} md={24} lg={12} span={12}>
                                                                   <div className={`${styles.topCardElement}`}>
                                                                       <div className={`${styles.topCardElementIconBadge}`}>
                                                                           <Image src={PlayImg}  alt={"Nombres de jeu"}></Image>
                                                                       </div>
                                                                       <div className={`${styles.topCardElementText}`}>
                                                                           <div className={`${styles.topCardElementTextDatesCounter}`}>
                                                                               {statsData["gameCount"]}
                                                                           </div>

                                                                           <div className={`${styles.topCardElementTextDatesTitle}`}>Nombres de jeu</div>
                                                                       </div>
                                                                   </div>
                                                               </Col>
                                                               <Col className={`w-100 d-flex`} sm={24} md={24} lg={12} span={12}>
                                                                   <div className={`${styles.topCardElement}`}>
                                                                       <div className={`${styles.topCardElementIconBadge}`}>
                                                                           <Image src={PriceImg}  alt={"Montants des gains "}></Image>
                                                                       </div>
                                                                       <div className={`${styles.topCardElementText}`}>
                                                                           <div className={`${styles.topCardElementTextDatesCounter}`}>
                                                                               {statsData["totalGainAmount"]}
                                                                               €</div>

                                                                           <div className={`${styles.topCardElementTextDatesTitle}`}>

                                                                               {(userRole !== "ROLE_CLIENT")&& (
                                                                                   <span>
                                                                                       Charges Totales
                                                                                   </span>
                                                                               )}

                                                                                 {(userRole === "ROLE_CLIENT")&& (
                                                                                      <span>
                                                                                        Montants des gains
                                                                                      </span>
                                                                                 )}

                                                                               </div>
                                                                       </div>
                                                                   </div>
                                                               </Col>
                                                           </Row>
                                                       </div>

                                                       <GameStatusesTendanceStatsChart key={`${gainByPrizeChartToggle }${Math.random().toString(36).substring(7)}`} dataChart={gainTendanceData}/>


                                                   </Col>
                                                   <Col className={`w-100 ${styles.statsCharts}`} sm={24} md={24} lg={12} span={6}>
                                                       <h5 className={`mt-5`}>
                                                           Répartition Des Tickets en Fonction de Statut
                                                       </h5>
                                                       <PrizesStatsByStatusesChart key={`${gainByPrizeChartToggle }${Math.random().toString(36).substring(7)}`} dataChart={ticketsByStatuses} />
                                                   </Col>
                                                   <Col className={`w-100 ${styles.statsCharts}`} sm={24} md={24} lg={12} span={6}>
                                                       <h5 className={`mt-5`}>
                                                           Analyse des Tickets
                                                       </h5>
                                                       <GamePlayedStatsChart key={`${gainByPrizeChartToggle }${Math.random().toString(36).substring(7)}`} dataChart={playGameTendanceData}/>

                                                       <PrizesCostTendance key={`${gainByPrizeChartToggle }${Math.random().toString(36).substring(7)}`} dataChart={prizesCostTendanceData}/>


                                                   </Col>
                                                   <Col className={`w-100 ${styles.statsCharts}`} sm={24} md={24} lg={12} span={6}>
                                                       <h5>
                                                           Répartition des Tickets Distribués
                                                       </h5>
                                                       <PrizesChartDoughunt key={`${gainByPrizeChartToggle }${Math.random().toString(36).substring(7)}`} dataChart={gainByPrizeData}/>

                                                   </Col>

                                                   {userRole != "ROLE_CLIENT" && (
                                                       <>
                                                   <Col className={`w-100 ${styles.statsCharts}`} sm={24} md={24} lg={12} span={6}>

                                                       <h5>
                                                           Participants Les Plus Engagés
                                                       </h5>
                                                       <TopTeenParticipants key={`${gainByPrizeChartToggle }${Math.random().toString(36).substring(7)}`} dataTable={topGainTableData} />


                                                               <h5 className={`mt-5`}>
                                                                   Répartition Des Cadeaux En Fonction De L'âge
                                                               </h5>
                                                               <PrizesStatsWithAgeChart key={`${gainByPrizeChartToggle }${Math.random().toString(36).substring(7)}`} dataChart={gainByAgeData} />
                                                   </Col>
                                                       </>
                                                   )}
                                                   <Col className={`w-100 ${styles.statsCharts}`} sm={24} md={24} lg={12} span={6}>

                                                       {userRole != "ROLE_CLIENT" && (
                                                           <>
                                                               <h5>
                                                                   Analyse des Participants
                                                               </h5>
                                                               <PrizesStatsWithSexChart key={`${gainByPrizeChartToggle }${Math.random().toString(36).substring(7)}`} dataChart={gainByGenderData}/>
                                                               <PrizeStatsByGenderByAgeChart key={`${gainByPrizeChartToggle }${Math.random().toString(36).substring(7)}`} dataChart={gainByGenderByAge}/>
                                                           </>
                                                       )}


                                                       {
                                                           userRole === "ROLE_ADMIN" && (
                                                               <>
                                                                   <CityStatsChart key={`${gainByPrizeChartToggle }${Math.random().toString(36).substring(7)}`} dataChart={gainByCityData}/>
                                                               </>
                                                           )
                                                       }


                                                       {(userRole === "ROLE_ADMIN" && searchForm.storeId=="" )&& (
                                                           <>
                                                               <h5>
                                                                   Répartition des Gagnants par Magasin
                                                               </h5>
                                                               <PrizesWinStatsByStore key={gainByPrizeChartToggle} dataChart={gainByStoresData}/>

                                                           </>
                                                       )}


                                                   </Col>

                                               </Row>
                                           </div>
                                       </Row>

                                   </div>



                       </div>

                   </div>
               </>
           )}
       </>
    );
}

export default HomePage;