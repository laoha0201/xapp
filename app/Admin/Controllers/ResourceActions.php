<?php

namespace App\Admin\Controllers;

trait ResourceActions
{
    /**
     * Update the specified resource in storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update($xapp_id,$id)
    {
        $this->xapp_id = $xapp_id;
		return $this->form()->update($id);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return mixed
     */
    public function store($xapp_id)
    {
        $this->xapp_id = $xapp_id;
		return $this->form()->store();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($xapp_id,$id)
    {
        if(empty($id)){
			$response = [
                'status'  => false,
                'message' => '未指定操作记录d',
            ];
			return response()->json($response);
        }        
		$this->xapp_id = $xapp_id;
		return $this->form()->destroy($id);
    }
}
