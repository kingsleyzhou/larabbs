<?php

namespace App\Http\Controllers;

use App\Models\Topic;
use App\Handlers\ImageUploadHandler;
use Auth;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\TopicRequest;

class TopicsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index', 'show']]);
    }

	public function index(Request $request,Topic $topic)
	{
		$topics = $topic->WithOrder($request->order)->paginate(15);
		return view('topics.index', compact('topics'));
	}

    public function show(Topic $topic)
    {
        return view('topics.show', compact('topic'));
    }

	public function create(Topic $topic)
	{
		$categories = Category::all();
		return view('topics.create_and_edit', compact('topic','categories'));
	}

	public function store(TopicRequest $request,Topic $topic)
	{
		$topic->fill($request->all());
		$topic->user_id = Auth::id();
		$topic->save();
		return redirect()->to($topic->link())->with('success','帖子创建成功');
	}

	public function edit(Topic $topic)
	{
        $this->authorize('update', $topic);
        $categories = Category::all();
		return view('topics.create_and_edit', compact('topic','categories'));
	}

	public function update(TopicRequest $request, Topic $topic)
	{
		$this->authorize('update', $topic);
		$topic->update($request->all());

		return redirect()->to($topic->link())->with('success','帖子更新成功');
	}

	public function destroy(Topic $topic)
	{
		$this->authorize('destroy', $topic);
		$topic->delete();

		return redirect()->route('topics.index')->with('message', 'Deleted successfully.');
	}
	public function uploadImage(Request $request,ImageUploadHandler $uploader){
			//初始化数据，默认失败
			$data = [
				'success'=>false,
				'msg'=>'上传失败',
				'file_path'=>''	
			];
			//判断是否有上传文件，并赋值给$file
			if ($file = $request->upload_file) {
				//图片保存到本地
				$result = $uploader->save($file,'topics',\Auth::id(),1024);
				if ($result) {
					$data['file_path'] = $result['path'];
					$data['success'] = true;
					$data['msg'] = '上传成功';
				}
			}
			return $data;
	}
}