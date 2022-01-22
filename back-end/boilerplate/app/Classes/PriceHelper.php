<?php

namespace App\Classes;

class PriceHelper
{
    /*
     * Todo: Coding Test for Technical Hires
     * Please read the instructions on the README.md
     * Your task is to write the functions for the PriceHelper class
     * A set of sample test cases and expected results can be found in PriceHelperTest
     */

    /**
     * Task: Given an associative array of minimum order quantities and their respective prices, write a function to return the unit price of an item based on the quantity.
     *
     * Question:
     * If I purchase 10,000 bicycles, the unit price of the 10,000th bicycle would be 1.50
     * If I purchase 10,001 bicycles, the unit price of the 10,001st bicycle would be 1.00
     * If I purchase 100,001 bicycles, what would be the unit price of the 100,001st bicycle?
     *
     * @param int $qty
     * @param array $tiers
     * @return float
     */
    public static function getUnitPriceTierAtQty(int $qty, array $tiers): float
    {
        krsort($tiers); //sort the key of the associative array via descending order

        foreach ($tiers as $tier_qty => $tier_value) {
            if ($qty == 0) {
                // if there is no quantity, the price should be 0
                return floatval(0);
            }
            if ($qty >= intval($tier_qty)) {
                // since the quantity is already sorted in descending value,
                // we can now check if the total quantity is more than or equal to
                // the tier value and find how much the price of each item costs
                return floatval($tier_value);
            }
        }
    }

    /**
     * Task: Given an associative array of minimum order quantities and their respective prices, write a function to return the total price of an order of items based on the quantity ordered
     *
     * Question:
     * If I purchase 10,000 bicycles, the total price would be 1.5 * 10,000 = $15,000
     * If I purchase 10,001 bicycles, the total price would be (1.5 * 10,000) + (1 * 1) = $15,001
     * If I purchase 100,001 bicycles, what would the total price be?
     *
     * @param int $qty
     * @param array $tiers
     * @return float
     */
    public static function getTotalPriceTierAtQty(int $qty, array $tiers): float
    {
        $tiers_keys = array_keys($tiers); //to access the value pair in the associative array
        ksort($tiers); //sort the key of the associative array via ascending order incase the inputs are random
        $totalprice = 0.0;

        for ($i = 0; $i < sizeof($tiers); $i++) {
            $qty_range = 0.0;
            $tier_quantity = $tiers_keys[$i];
            $current_tier_price = $tiers[$tiers_keys[$i]];

            if ($i == sizeof($tiers) - 1 || $qty < ($tiers_keys[$i + 1] - $tier_quantity)) {
                //enter this block if there are no more tiers or the total amount quantity is lesser than the range
                //since this is the remaining quantity is either lesser or there are no more tiers, we can use the remaining amount and multiply by the price

                $totalprice = $totalprice +  $qty * $current_tier_price;
                return $totalprice;
            }

            if ($tier_quantity <= 0) {
                //if the tier quantity is 0 then we find the first range
                $qty_range = $tiers_keys[$i + 1] - 1;

                //Update the total quantity and start append it to the total price
                $qty = $qty - $qty_range;
                $totalprice = $totalprice + ($qty_range * self::getUnitPriceTierAtQty($qty_range, $tiers));
            } else {
                //find out the range
                $qty_range = $tiers_keys[$i + 1] - $tier_quantity;

                //Update the total quantity and start append it to the total price
                $qty = $qty - $qty_range;
                $totalprice = $totalprice + ($qty_range * self::getUnitPriceTierAtQty($qty_range, $tiers));
            }
        }
    }

    /**
     * Task: Given an array of quantity of items ordered per month and an associative array of minimum order quantities and their respective prices, write a function to return an array of total charges incurred per month. Each item in the array should reflect the total amount the user has to pay for that month.
     *
     * Question A:
     * A user purchased 933, 22012, 24791 and 15553 bicycles respectively in Jan, Feb, Mar, April
     * The management would like to know how much to bill this user for each of those month.
     * This user is on a special pricing tier where the quantity does not reset each month and is thus CUMULATIVE.
     *
     * Question B:
     * A user purchased 933, 22012, 24791 and 15553 bicycles respectively in Jan, Feb, Mar, April
     * The management would like to know how much to bill this user for each of those month.
     * This user is on the typical pricing tier where the quantity RESETS each month and is thus NOT CUMULATIVE.
     *
     */
    public static function getPriceAtEachQty(array $qtyArr, array $tiers, bool $cumulative = false): array
    {
        $finalArray = [];

        if ($cumulative == true) {
            $cum = 0;
            foreach ($qtyArr as $q) {
                $price = self::getTotalPriceTierAtQty($q + $cum, $tiers) - self::getTotalPriceTierAtQty($cum, $tiers);
                $cum += $q;
                array_push($finalArray, $price);
            }
        } else {
            //this section onwards is for non-cumulative calculation
            for ($i = 0; $i < sizeof($qtyArr); $i++) {
                //get the price based on Question B's function
                $price = self::getTotalPriceTierAtQty($qtyArr[$i], $tiers);

                //push the price into the final array
                array_push($finalArray, $price);
            }
        }
        return $finalArray;
    }
}
