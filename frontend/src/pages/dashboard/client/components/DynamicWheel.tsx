import React, {useEffect, useState} from "react";
import styles from "@/styles/pages/dashboards/clientDashboard.module.css";
import {Button} from "antd";
import {RedoOutlined} from "@ant-design/icons";
import PingImage from '../../../../../../public/images/ping.png';
import Image from 'next/image';
interface DynamicWheelProps {
    data: any;
    playGame: boolean;
    onFinishedWheel: () => void;
    winningSegment : any;
}

const DynamicWheel = ({ data, playGame , onFinishedWheel , winningSegment }: DynamicWheelProps) => {
    const [Wheel, setWheel] = useState(null);
    const [mustSpin, setMustSpin] = useState(false);
    const [prizeNumber, setPrizeNumber] = useState(0);
    const [rerender, setRerender] = useState(0);

    const handleSpinClick = () => {
        const options = [
            { option: 'Infuseur à thé' },
            { option: '100g d’un thé détox ou d’infusion' },
            { option: '100g d’un thé signature' },
            { option: 'Coffret à 39€' },
            { option: 'Coffret à 69€' },
        ];

        setMustSpin(false);
        if (!mustSpin) {
            const newPrizeNumber = Math.floor(Math.random() * options.length);
            setMustSpin(true);
        }
    };


    const pointerProps = {
        src: "/images/pin.png",
        style: { transform: "rotate(45deg)" , top: "0", right: "12%" },
    };

    useEffect(() => {

        const loadDynamicWheel = async () => {
            try {
                const module = await import("react-custom-roulette");
                const { Wheel } = module;

                setWheel(
                    <Wheel
                        startingOptionIndex={Math.floor(Math.random() * data.length)}
                        mustStartSpinning={mustSpin}
                        prizeNumber={parseInt(winningSegment)-1}
                        data={data}
                        outerBorderColor="#70a0ff"
                        outerBorderWidth={15}
                        innerRadius={10}
                        innerBorderColor="#70a0ff"
                        innerBorderWidth={4}
                        radiusLineWidth={0}
                        radiusLineColor="#70a0ff"
                        perpendicularText={true}
                        pointerProps={pointerProps}
                        onStopSpinning={() => {
                            onFinishedWheel();
                        }}



                    /> as any
                );
                setRerender((prev) => prev + 1);
            } catch (err) {
                console.error(err);
            }
        };

        loadDynamicWheel();
    }, [mustSpin, prizeNumber]);

    return (
        <div className={`${styles.wheelDiv}`}>
            <div style={{position: "relative"}}>
                {Wheel}
                <button
                    onClick={handleSpinClick}
                    style={{
                        position: "absolute",
                        top: "50%",
                        left: "50%",
                        transform: "translate(-50%, -50%)",
                        width: "80px",
                        height: "80px",
                        background: "white",
                        borderRadius: "50%",
                        border: "4px solid #70a0ff",
                        zIndex: 40,
                        fontSize: "24px",
                        fontWeight: "bold",
                    }}
                >
                    <RedoOutlined className={`mx-2`}
                                    style={{fontSize: "44px", color: "#70a0ff" , fontWeight: "bold"}}
                    />
                </button>
            </div>
        </div>
    );
};

export default DynamicWheel;
