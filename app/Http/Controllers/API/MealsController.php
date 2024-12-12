<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Meal;
use App\Models\ActiveMeals;
use Illuminate\Support\Facades\DB;
use App\OpenApi\Parameters\Meals\CreateMealsParameters;
use Vyuldashev\LaravelOpenApi\Attributes as OpenApi;
use Carbon\Carbon;
use Illuminate\Support\Arr;

#[OpenApi\PathItem]
class MealsController extends BaseController {
    //
    /**
     * Retrieves all Meals.
     *
     * Returns meals
     */
    #[OpenApi\Operation(tags: ['Meals'])]
    public function meals(){

        $meal = Meal::get();

        $output = [
            'meals' => $meal,
        ];

        return $this->sendResponse($output, 'Success all meals returned');
    }

    /**
     * Retrieves all Meals.
     *
     * Returns meals
     */
    #[OpenApi\Operation(tags: ['Meals'])]
    #[OpenApi\Parameters(factory: CreateMealsParameters::class)]
    public function createMeals(Request $request){
        $request->validate([
            'Cuisine' => 'required|string|max:255',
            'Category' => 'required|string|max:255',
            'Calories' => 'required|string|max:10',
            'Description' => 'required|string|max:255',
            'ProductID' => 'required|string|max:6|min:4',
        ]);

        $meal = Meal::create([
            'Cuisine' => $request->Cuisine,
            'Category' => $request->Category,
            'Calories' => $request->Calories,
            'Description' => $request->Description,
            'TotalFat' => $request->TotalFat,
            'TotalCarbs' => $request->TotalCarbs,
            'Sodium' => $request->Sodium,
            'Protein' => $request->Protein,
            'MealType' => $request->MealType,
            'ProductID' => $request->ProductID,
            'Price' => $request->Price,
            'Status' => "Not confirmend",
        ]);

        if($meal->Status == 'Active'){
            ActiveMeals::create([
                'Cuisine' => $request->Cuisine,
                'Category' => $request->Category,
                'Calories' => $request->Calories,
                'Description' => $request->Description,
                'TotalFat' => $request->TotalFat,
                'TotalCarbs' => $request->TotalCarbs,
                'Sodium' => $request->Sodium,
                'Protein' => $request->Protein,
                'MealType' => $request->MealType,
                'ProductID' => $request->ProductID,
                'Price' => $request->Price,
                'Status' => "Active",
            ]);
        }
        // Add new meal to Mongodb meals table.

        $success['token'] =  $meal->Cuisine;
        return $this->sendResponse($success, 'Meal successfully created');
    }

    /**
     * Edit Meals.
     *
     * Returns meals
     */
    #[OpenApi\Operation(tags: ['Meals'])]
    public function editMeals(Meal $meal){

        $ml = Meal::find($meal);

        $output = [
            'meals' => $ml,
        ];
        return $this->sendResponse($output, 'Meal retrieved succesfully');
    }

    /**
     * Update Meals.
     *
     * Returns meals
     */
    #[OpenApi\Operation(tags: ['Meals'])]
    // #[OpenApi\Parameters(factory: CreateMealsParameters::class)]
    public function updateMeals(Request $request, $id) {
        $a = $request->all();
        $ml = Meal::find($id);
        if($a['Cuisine'] != Null){$ml->Cuisine = $a['Cuisine'];}
        if($a['Category'] != Null){$ml->Category = $a['Category'];}
        if($a['Calories'] != Null){$ml->Calories = $a['Calories'];}
        if($a['Description'] != Null){$ml->Description = $a['Description'];}
        if($a['Price'] != Null){$ml->Price = $a['Price'];}
        if($a['TotalFat'] != Null){$ml->TotalFat = $a['TotalFat'];}
        if($a['TotalCarbs'] != Null){$ml->TotalCarbs = $a['TotalCarbs'];}
        if($a['Sodium'] != Null){$ml->Sodium = $a['Sodium'];}
        if($a['Protein'] != Null){$ml->Protein = $a['Protein'];}
        if($a['ProductID'] != Null){$ml->ProductID = $a['ProductID'];}
        if($a['Price'] != Null){$ml->ProductID = $a['Price'];}
        if($a['MealType'] != Null){$ml->MealType = $a['MealType'];}
        if($a['Status'] != Null){$ml->Status = $a['Status'];}
        $ml->save();

        // update Active meals on Consumer table
        if($ml->Status == 'Active'){
            ActiveMeals::where('Cuisine', $a['Cuisine'])->update([
                'Cuisine' => $request->Cuisine,
                'Category' => $request->Category,
                'Calories' => $request->Calories,
                'Description' => $request->Description,
                'TotalFat' => $request->TotalFat,
                'TotalCarbs' => $request->TotalCarbs,
                'Sodium' => $request->Sodium,
                'Protein' => $request->Protein,
                'MealType' => $request->MealType,
                'ProductID' => $request->ProductID,
                'Price' => $request->Price,
                'Status' => $request->Status,
            ]);
        }

        $output = [
            'meals' => $ml,
        ];
        return $this->sendResponse($output, 'Meal retrieved succesfully');
    }

    /**
     * Update Meals status.
     *
     * update for Meal Status
     */
    #[OpenApi\Operation(tags: ['Meals'])]
    public function statusUpdateMeal(Request $request, Meal $meal) {
        $k = $request->all();
        $mealID = $meal->id; 
        DB::table('Meals')
        ->updateOrInsert(
            ['id' => $meal->MachineID, 'KioskNumber' => $meal->KioskNumber],
            ['Status' => $k["Status"]]
        );

        ActiveMeals::where('id', $$mealID)->update([
            'Status' => $request->Status,
        ]);


        $output = $meal->id;
    
        return $this->sendResponse($output, 'status has been Updated');
    }

    /**
     * Destroy Meal.
     *
     * Deleted meal response
     */
    #[OpenApi\Operation(tags: ['Meals'])]
    public function delete($id) {
        $meal = Meal::destroy($id);

        $output = [
            'meals' => 'Success',
        ];
        return $this->sendResponse($output, 'Meal has been deleted');
    }

    public function mealsListBydate(Request $request){
        $startDate = $request->Start_date;
        $endDate = $request->End_date;
        $meals = DB::table('meals')->get();

        $dateSales = [];
        foreach($meals as $meal) {
            // dd($kiosk->KioskNumber);

            // Total number for porductID's Stocked in all kiosks
            $a = DB::table('kiosk_meal')->where('ProductID', $meal->ProductID)->get();
            $totalProduct = $a->count();
            //Total number of kiosk with productID
            $kioskcount = $a->unique('kiosk_id')->count();

            // find orders with productID and kiosk_id that have sold
            $mealSold = DB::table('orders')
                ->where('ProductID', $meal->ProductID)
                ->whereBetween('Time', [$startDate, $endDate])
                ->sum('Quantity'); 
            
            //Meals left in the field
            $mealsLeft = $totalProduct - $mealSold; 
            $currentmeal = $meal->Cuisine; 

            $dateSales[] = Arr::add(['mealName' => $currentmeal, 'kiosk_total' => $kioskcount, 'Total_meals' => $totalProduct], 'mealsInField', $mealsLeft );
        }
        $output = $dateSales;
        return $this->sendResponse($output, 'Todays meals detail retrieved successfully.'); 
    }

    public function mealsListToday() {
        $meals = DB::table('meals')->get();
        // dd($kiosk[0]->KioskNumber);
        $today = Carbon::now();
        $todaySales = [];
        foreach($meals as $meal) {
            // dd($kiosk->KioskNumber);

            // Total number for porductID's Stocked in all kiosks
            $a = DB::table('load_deliveries')->where('ProductID', $meal->ProductID)->get();
            $totalProduct = $a->count();
            //Total number of kiosk with productID
            $kioskcount = $a->unique('kiosk_id')->count();

            // find orders with productID and kiosk_id that have sold
            $mealSold = DB::table('orders')
                ->where('ProductID', $meal->ProductID)
                ->where('Time', $today)
                ->sum('Quantity'); 
            
            
            //Meals left in the field
            $mealsLeft = $totalProduct - $mealSold;
            $currentmeal = $meal->Cuisine; 

            $todaySales[] = Arr::add(['mealName' => $currentmeal, 'kiosk_total' => $kioskcount, 'Total_meals' => $totalProduct], 'mealsInField', $mealsLeft );
        }
        $output = $todaySales;
        return $this->sendResponse($output, 'Todays meals detail retrieved successfully.');   
    }
    


}
