<?php
/*   	William Habsburg 000869622 
		Assignment 5
        This program describes the Stock Record class
*/

// A class for stock records, set up for JSON use
class StRec implements JsonSerializable {
    private $StockId;
    private $StockName;
    private $CurrentPrice;
    private $UpdateDateTime;

    // The constructor for the stock
    public function __construct($StockId, $StockName, $CurrentPrice, $UpdateDateTime)
    {
        $this->StockId = $StockId;
        $this->StockName = $StockName;
        $this->CurrentPrice = $CurrentPrice;
        $this->UpdateDateTime = $UpdateDateTime;
    }

    /**
     * Called by json_encode
     * This returns the object's variables
     */
    public function jsonSerialize()
    {
        return  get_object_vars($this);
    }
}

?>
